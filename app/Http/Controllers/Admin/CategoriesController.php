<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lastSortOrder = Category::max('sort_order');

        return view('admin.categories.index', [
            'categoryCount' => Category::count(),
            'nextSortOrder' => $lastSortOrder === null ? 0 : (int) $lastSortOrder + 1,
        ]);
    }

    public function data()
    {
        $query = Category::query()
            ->select([
                'id',
                'name',
                'slug',
                'description',
                'image',
                'is_active',
                'sort_order',
                'created_at',
            ]);

        return DataTables::eloquent($query)
            ->addIndexColumn()


            ->addColumn('image_preview', function ($category) {
                if (!$category->image) {
                    return '
            <div class="category-image-soft is-empty" aria-label="Belum ada gambar category">
                <i class="far fa-image"></i>
            </div>
        ';
                }

                $url = asset('storage/' . $category->image);

                return '
        <button type="button" class="category-image-soft" data-category-image-preview data-image-url="' . e($url) . '" data-image-name="' . e($category->name) . '" aria-label="Lihat gambar ' . e($category->name) . '" style="background-image:linear-gradient(145deg,rgba(255,255,255,.08),rgba(111,66,193,.12)),url(&quot;' . e($url) . '&quot;)">
            <span class="category-image-soft-icon"><i class="fas fa-expand-alt"></i></span>
        </button>
    ';
            })


            ->addColumn('status', function ($category) {
                return $category->is_active
                    ? '<span class="badge badge-success">Aktif</span>'
                    : '<span class="badge badge-secondary">Nonaktif</span>';
            })

            ->addColumn('actions', function ($category) {
                return '
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button"
                            data-action="edit"
                            data-id="' . $category->id . '"
                            class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit
                        </button>

                        <button type="button"
                            data-action="delete"
                            data-id="' . $category->id . '"
                            class="btn btn-outline-danger">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                ';
            })

            ->rawColumns(['image_preview', 'status', 'actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'sort_order_custom' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = $this->generateSlug($validated['name']);
        $customSortOrder = $request->boolean('sort_order_custom');
        unset($validated['sort_order_custom']);
        $uploadedImage = $request->hasFile('image')
            ? $request->file('image')->store('categories', 'public')
            : null;

        if ($uploadedImage) {
            $validated['image'] = $uploadedImage;
        }

        try {
            DB::transaction(function () use (&$validated, $customSortOrder) {
                $categories = Category::query()
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                $lastSortOrder = Category::max('sort_order');
                $nextSortOrder = $lastSortOrder === null ? 0 : (int) $lastSortOrder + 1;

                foreach ($categories as $index => $category) {
                    if ($category->sort_order !== $index) {
                        $category->update(['sort_order' => $index]);
                    }
                }

                $requestedPosition = $customSortOrder ? (int) $validated['sort_order'] : $nextSortOrder;
                $position = min($requestedPosition, $categories->count());
                for ($index = $categories->count() - 1; $index >= $position; $index--) {
                    $categories[$index]->update(['sort_order' => $index + 1]);
                }

                $validated['sort_order'] = $position;
                Category::create($validated);
            });
        } catch (Throwable $exception) {
            if ($uploadedImage) {
                Storage::disk('public')->delete($uploadedImage);
            }

            throw $exception;
        }

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan.',
        ]);
    }


    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'sort_order_custom' => ['nullable', 'boolean'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = $this->generateSlug(
            $validated['name'],
            $category->id
        );

        $removeImage = $request->boolean('remove_image');
        $customSortOrder = $request->boolean('sort_order_custom');
        unset($validated['remove_image'], $validated['sort_order_custom']);

        $oldImage = $category->image;
        $uploadedImage = $request->hasFile('image')
            ? $request->file('image')->store('categories', 'public')
            : null;

        if ($uploadedImage) {
            $validated['image'] = $uploadedImage;
        } elseif ($removeImage) {
            $validated['image'] = null;
        } else {
            unset($validated['image']);
        }

        try {
            DB::transaction(function () use ($category, &$validated, $customSortOrder) {
                $categories = Category::query()
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                $oldPosition = $categories->search(fn ($item) => $item->id === $category->id);
                foreach ($categories as $index => $item) {
                    if ($item->sort_order !== $index) {
                        $item->update(['sort_order' => $index]);
                    }
                }

                $lastPosition = max(0, $categories->count() - 1);
                $requestedPosition = $customSortOrder ? (int) $validated['sort_order'] : $oldPosition;
                $newPosition = min($requestedPosition, $lastPosition);

                if ($newPosition < $oldPosition) {
                    for ($index = $oldPosition - 1; $index >= $newPosition; $index--) {
                        $categories[$index]->update(['sort_order' => $index + 1]);
                    }
                } elseif ($newPosition > $oldPosition) {
                    for ($index = $oldPosition + 1; $index <= $newPosition; $index++) {
                        $categories[$index]->update(['sort_order' => $index - 1]);
                    }
                }

                $validated['sort_order'] = $newPosition;
                $category->update($validated);
            });
        } catch (Throwable $exception) {
            if ($uploadedImage) {
                Storage::disk('public')->delete($uploadedImage);
            }

            throw $exception;
        }

        if ($oldImage && ($uploadedImage || $removeImage)) {
            Storage::disk('public')->delete($oldImage);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui.',
        ]);
    }


    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak dapat dihapus karena masih memiliki produk.',
            ], 422);
        }

        $image = $category->image;
        DB::transaction(function () use ($category) {
            $category->delete();

            $categories = Category::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($categories as $index => $item) {
                if ($item->sort_order !== $index) {
                    $item->update(['sort_order' => $index]);
                }
            }
        });

        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }

    private function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Category::where('slug', $slug)
            ->when(
                $ignoreId,
                fn($query) => $query->where('id', '!=', $ignoreId)
            )
            ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }
}

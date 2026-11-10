<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.categories.index');
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
            <div class="d-flex align-items-center justify-content-center rounded bg-light text-muted" style="width:48px;height:48px">
                <i class="far fa-image"></i>
            </div>
        ';
                }

                $url = asset('storage/' . $category->image);

                return '
        <div class="d-flex align-items-center justify-content-center overflow-hidden rounded border">
            <img
                src="' . e($url) . '"
                alt="' . e($category->name) . '"
                width="48"
                height="48"
                style="width:48px;height:48px;max-width:48px;max-height:48px;object-fit:cover;display:block;"
            >
        </div>
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
        ]);

        $validated['slug'] = $this->generateSlug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('categories', 'public');
        }

        Category::create($validated);

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
            'remove_image' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = $this->generateSlug(
            $validated['name'],
            $category->id
        );

        $removeImage = $request->boolean('remove_image');

        unset($validated['remove_image']);

        // Jika mengunggah gambar baru, gambar lama diganti.
        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $validated['image'] = $request->file('image')
                ->store('categories', 'public');
        }

        // Jika tidak mengunggah gambar baru tetapi memilih hapus gambar.
        elseif ($removeImage) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $validated['image'] = null;
        }

        // Jika tidak ada perubahan gambar, pertahankan gambar lama.
        else {
            unset($validated['image']);
        }

        $category->update($validated);

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

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

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

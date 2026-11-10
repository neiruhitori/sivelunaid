<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class ProductsController extends Controller
{
    public function index()
    {
        $lastSortOrder = Product::max('sort_order');

        return view('admin.products.index', [
            'categories' => Category::query()->orderBy('sort_order')->orderBy('name')->get(['id', 'name']),
            'productCount' => Product::count(),
            'nextSortOrder' => $lastSortOrder === null ? 0 : (int) $lastSortOrder + 1,
        ]);
    }

    public function data()
    {
        $query = Product::query()
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select(['products.*', 'categories.name as category_name']);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('image_preview', function (Product $product) {
                if (!$product->image) {
                    return '<div class="d-flex align-items-center justify-content-center rounded bg-light text-muted" style="width:48px;height:48px"><i class="far fa-image"></i></div>';
                }

                $url = asset('storage/' . $product->image);

                return '<img src="' . e($url) . '" alt="' . e($product->name) . '" width="48" height="48" class="rounded border" style="width:48px;height:48px;object-fit:cover">';
            })
            ->addColumn('featured_status', fn (Product $product) => $product->is_featured
                ? '<span class="badge badge-warning"><i class="fas fa-star mr-1"></i>Unggulan</span>'
                : '<span class="badge badge-light">Reguler</span>')
            ->addColumn('status', fn (Product $product) => $product->is_active
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-secondary">Nonaktif</span>')
            ->addColumn('actions', fn (Product $product) => '
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" data-action="edit" data-id="' . $product->id . '" class="btn btn-outline-primary"><i class="fas fa-edit"></i> Edit</button>
                    <button type="button" data-action="delete" data-id="' . $product->id . '" class="btn btn-outline-danger"><i class="fas fa-trash"></i> Hapus</button>
                </div>')
            ->rawColumns(['image_preview', 'featured_status', 'status', 'actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_featured' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'sort_order_custom' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = $this->generateSlug($validated['name']);
        $customSortOrder = $request->boolean('sort_order_custom');
        unset($validated['sort_order_custom']);

        $uploadedImage = $request->hasFile('image')
            ? $request->file('image')->store('products', 'public')
            : null;
        if ($uploadedImage) {
            $validated['image'] = $uploadedImage;
        }

        try {
            DB::transaction(function () use (&$validated, $customSortOrder) {
                $products = Product::query()
                    ->orderBy('sort_order')->orderBy('id')->lockForUpdate()->get();
                $lastSortOrder = Product::max('sort_order');
                $nextSortOrder = $lastSortOrder === null ? 0 : (int) $lastSortOrder + 1;

                foreach ($products as $index => $product) {
                    if ($product->sort_order !== $index) {
                        $product->update(['sort_order' => $index]);
                    }
                }

                $requestedPosition = $customSortOrder ? (int) $validated['sort_order'] : $nextSortOrder;
                $position = min($requestedPosition, $products->count());
                for ($index = $products->count() - 1; $index >= $position; $index--) {
                    $products[$index]->update(['sort_order' => $index + 1]);
                }

                $validated['sort_order'] = $position;
                Product::create($validated);
            });
        } catch (Throwable $exception) {
            if ($uploadedImage) {
                Storage::disk('public')->delete($uploadedImage);
            }

            throw $exception;
        }

        return response()->json(['success' => true, 'message' => 'Produk berhasil ditambahkan.']);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_featured' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'sort_order_custom' => ['nullable', 'boolean'],
            'remove_image' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = $this->generateSlug($validated['name'], $product->id);
        $customSortOrder = $request->boolean('sort_order_custom');
        $removeImage = $request->boolean('remove_image');
        unset($validated['sort_order_custom'], $validated['remove_image']);

        $oldImage = $product->image;
        $uploadedImage = $request->hasFile('image')
            ? $request->file('image')->store('products', 'public')
            : null;

        if ($uploadedImage) {
            $validated['image'] = $uploadedImage;
        } elseif ($removeImage) {
            $validated['image'] = null;
        } else {
            unset($validated['image']);
        }

        try {
            DB::transaction(function () use ($product, &$validated, $customSortOrder) {
                $products = Product::query()
                    ->orderBy('sort_order')->orderBy('id')->lockForUpdate()->get();
                $oldPosition = $products->search(fn ($item) => $item->id === $product->id);

                foreach ($products as $index => $item) {
                    if ($item->sort_order !== $index) {
                        $item->update(['sort_order' => $index]);
                    }
                }

                $lastPosition = max(0, $products->count() - 1);
                $requestedPosition = $customSortOrder ? (int) $validated['sort_order'] : $oldPosition;
                $newPosition = min($requestedPosition, $lastPosition);

                if ($newPosition < $oldPosition) {
                    for ($index = $oldPosition - 1; $index >= $newPosition; $index--) {
                        $products[$index]->update(['sort_order' => $index + 1]);
                    }
                } elseif ($newPosition > $oldPosition) {
                    for ($index = $oldPosition + 1; $index <= $newPosition; $index++) {
                        $products[$index]->update(['sort_order' => $index - 1]);
                    }
                }

                $validated['sort_order'] = $newPosition;
                $product->update($validated);
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

        return response()->json(['success' => true, 'message' => 'Produk berhasil diperbarui.']);
    }

    public function destroy(Product $product)
    {
        $image = $product->image;

        DB::transaction(function () use ($product) {
            $product->delete();
            $products = Product::query()->orderBy('sort_order')->orderBy('id')->lockForUpdate()->get();

            foreach ($products as $index => $item) {
                if ($item->sort_order !== $index) {
                    $item->update(['sort_order' => $index]);
                }
            }
        });

        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus.']);
    }

    private function generateSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Product::query()->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }
}

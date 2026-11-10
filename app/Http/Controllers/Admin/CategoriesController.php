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
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-gray-400">
                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M3 16.5V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10.5m-16 0 4.5-4.5 3 3 3-3 5.5 5.5M3 16.5V18a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-1.5" />
                </svg>
            </div>
        ';
                }

                $url = asset('storage/' . $category->image);

                return '
        <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-xl border border-gray-100 bg-gray-50">
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
                    ? '<span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Aktif</span>'
                    : '<span class="inline-flex rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Nonaktif</span>';
            })

            ->addColumn('actions', function ($category) {
                return '
                    <div class="flex items-center gap-2">
                        <button type="button"
                            data-action="edit"
                            data-id="' . $category->id . '"
                            class="rounded-lg bg-amber-100 px-3 py-1.5 text-xs font-medium text-amber-700 hover:bg-amber-200">
                            Edit
                        </button>

                        <button type="button"
                            data-action="delete"
                            data-id="' . $category->id . '"
                            class="rounded-lg bg-red-100 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-200">
                            Hapus
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

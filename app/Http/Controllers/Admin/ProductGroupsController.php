<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductGroupsController extends Controller
{
    public function index()
    {
        $lastSortOrder = ProductGroup::max('sort_order');

        return view('admin.product-groups.index', [
            'products' => Product::query()->orderBy('name')->get(['id', 'name']),
            'nextSortOrder' => $lastSortOrder === null ? 0 : (int) $lastSortOrder + 1,
        ]);
    }

    public function data()
    {
        $query = ProductGroup::query()
            ->join('products', 'products.id', '=', 'product_groups.product_id')
            ->select(['product_groups.*', 'products.name as product_name']);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('description', fn (ProductGroup $group) => $group->description)
            ->addColumn('banner_description', fn (ProductGroup $group) => $group->banner_description)
            ->addColumn('status', fn (ProductGroup $group) => $group->is_active
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-secondary">Nonaktif</span>')
            ->editColumn('created_at', fn (ProductGroup $group) => $group->created_at?->format('d/m/Y H:i'))
            ->addColumn('actions', fn (ProductGroup $group) => '
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" data-action="edit" data-id="' . $group->id . '" class="btn btn-outline-primary"><i class="fas fa-edit"></i> Edit</button>
                    <button type="button" data-action="delete" data-id="' . $group->id . '" class="btn btn-outline-danger"><i class="fas fa-trash"></i> Hapus</button>
                </div>')
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }

    public function create()
    {
        return redirect()->route('admin.product-groups.index');
    }

    public function store(Request $request)
    {
        $validated = $this->validateGroup($request);
        $names = $validated['names'];
        unset($validated['names'], $validated['sort_order_custom']);
        $customSortOrder = $request->boolean('sort_order_custom');

        DB::transaction(function () use ($validated, $names, $customSortOrder) {
            $groups = $this->orderedGroups((int) $validated['product_id']);
            $position = min($customSortOrder ? (int) $validated['sort_order'] : $groups->count(), $groups->count());
            $batch = collect($names)->map(fn ($name) => new ProductGroup(array_merge($validated, ['name' => $name])));
            $groups->splice($position, 0, $batch->all());
            $groups->each(function (ProductGroup $group, int $index) {
                $group->sort_order = $index;
                $group->save();
            });
        });

        return response()->json(['success' => true, 'message' => count($names) . ' Product Group berhasil ditambahkan.']);
    }

    public function show(ProductGroup $productGroup)
    {
        return redirect()->route('admin.product-groups.index');
    }

    public function edit(ProductGroup $productGroup)
    {
        return redirect()->route('admin.product-groups.index');
    }

    public function update(Request $request, ProductGroup $productGroup)
    {
        $validated = $this->validateGroup($request);
        $names = $validated['names'];
        unset($validated['names'], $validated['sort_order_custom']);
        $customSortOrder = $request->boolean('sort_order_custom');

        DB::transaction(function () use ($productGroup, $validated, $names, $customSortOrder) {
            $oldProductId = (int) $productGroup->product_id;
            $newProductId = (int) $validated['product_id'];
            $oldGroups = $this->orderedGroups($oldProductId);
            $oldIndex = $oldGroups->search(fn ($group) => $group->id === $productGroup->id);

            if ($newProductId !== $oldProductId) {
                $oldGroups->reject(fn ($group) => $group->id === $productGroup->id)->values()
                    ->each(fn ($group, $index) => $group->update(['sort_order' => $index]));
                $siblings = $this->orderedGroups($newProductId);
                $position = min($customSortOrder ? (int) $validated['sort_order'] : $siblings->count(), $siblings->count());
            } else {
                $siblings = $oldGroups->reject(fn ($group) => $group->id === $productGroup->id)->values();
                $position = min($customSortOrder ? (int) $validated['sort_order'] : $oldIndex, $siblings->count());
            }

            $batch = collect($names)->map(fn ($name, $index) => $index === 0
                ? $productGroup
                : new ProductGroup());
            $siblings->splice($position, 0, $batch->all());

            foreach ($siblings as $index => $group) {
                if ($group->id === $productGroup->id) {
                    $group->fill(array_merge($validated, ['name' => $names[0], 'sort_order' => $index]))->save();
                } elseif (!$group->exists) {
                    $group->fill(array_merge($validated, ['name' => $names[$index - $position], 'sort_order' => $index]))->save();
                } else {
                    $group->update(['sort_order' => $index]);
                }
            }
        });

        return response()->json(['success' => true, 'message' => count($names) . ' Product Group berhasil disimpan.']);
    }

    public function destroy(ProductGroup $productGroup)
    {
        DB::transaction(function () use ($productGroup) {
            $productId = (int) $productGroup->product_id;
            $productGroup->delete();
            $this->orderedGroups($productId)->each(fn ($group, $index) => $group->update(['sort_order' => $index]));
        });

        return response()->json(['success' => true, 'message' => 'Product Group berhasil dihapus.']);
    }

    private function validateGroup(Request $request): array
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'names' => ['required', 'array', 'min:1', 'max:50'],
            'names.*' => ['required', 'string', 'max:150'],
            'description_items' => ['nullable', 'array', 'max:100'],
            'description_items.*' => ['nullable', 'string', 'max:2000'],
            'banner_description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'sort_order_custom' => ['nullable', 'boolean'],
        ]);

        $descriptionItems = collect($validated['description_items'] ?? [])
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values();

        unset($validated['description_items']);
        $validated['description'] = $descriptionItems->isNotEmpty()
            ? $descriptionItems->implode("\n")
            : null;

        return $validated;
    }

    private function orderedGroups(int $productId)
    {
        return ProductGroup::query()->where('product_id', $productId)
            ->orderBy('sort_order')->orderBy('id')->lockForUpdate()->get();
    }
}

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
    /**
     * Display a listing of the resource.
     */
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
            ->addColumn('description', fn (ProductGroup $group) => $group->description ?: '-')
            ->addColumn('status', fn (ProductGroup $group) => $group->is_active
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-secondary">Nonaktif</span>')
            ->addColumn('created_at', fn (ProductGroup $group) => $group->created_at?->format('d/m/Y H:i'))
            ->addColumn('actions', fn (ProductGroup $group) => '
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" data-action="edit" data-id="' . $group->id . '" class="btn btn-outline-primary"><i class="fas fa-edit"></i> Edit</button>
                    <button type="button" data-action="delete" data-id="' . $group->id . '" class="btn btn-outline-danger"><i class="fas fa-trash"></i> Hapus</button>
                </div>')
            ->rawColumns(['status', 'actions'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('admin.product-groups.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'sort_order_custom' => ['nullable', 'boolean'],
        ]);

        $customSortOrder = $request->boolean('sort_order_custom');
        unset($validated['sort_order_custom']);

        DB::transaction(function () use ($validated, $customSortOrder) {
            $groups = ProductGroup::query()->where('product_id', $validated['product_id'])
                ->orderBy('sort_order')->orderBy('id')->lockForUpdate()->get();
            $requested = $customSortOrder ? (int) $validated['sort_order'] : $groups->count();
            $position = min($requested, $groups->count());

            foreach ($groups as $index => $group) {
                $group->update(['sort_order' => $index >= $position ? $index + 1 : $index]);
            }

            $validated['sort_order'] = $position;
            ProductGroup::create($validated);
        });

        return response()->json(['success' => true, 'message' => 'Product Group berhasil ditambahkan.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductGroup $productGroup)
    {
        return redirect()->route('admin.product-groups.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductGroup $productGroup)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'sort_order_custom' => ['nullable', 'boolean'],
        ]);

        $customSortOrder = $request->boolean('sort_order_custom');
        DB::transaction(function () use ($productGroup, $validated, $customSortOrder) {
            $oldProductId = $productGroup->product_id;
            $oldGroups = ProductGroup::query()->where('product_id', $oldProductId)
                ->orderBy('sort_order')->orderBy('id')->lockForUpdate()->get();
            $oldIndex = $oldGroups->search(fn ($group) => $group->id === $productGroup->id);

            if ((int) $validated['product_id'] !== (int) $oldProductId) {
                $oldGroups->reject(fn ($group) => $group->id === $productGroup->id)
                    ->values()->each(fn ($group, $index) => $group->update(['sort_order' => $index]));
                $newGroups = ProductGroup::query()->where('product_id', $validated['product_id'])
                    ->orderBy('sort_order')->orderBy('id')->lockForUpdate()->get();
                $position = min($customSortOrder ? (int) $validated['sort_order'] : $newGroups->count(), $newGroups->count());
                $newGroups->splice($position, 0, [$productGroup]);
                $newGroups->each(fn ($group, $index) => $group->update(['sort_order' => $index]));
            } else {
                $position = min($customSortOrder ? (int) $validated['sort_order'] : $oldIndex, max(0, $oldGroups->count() - 1));
                $orderedGroups = $oldGroups->reject(fn ($group) => $group->id === $productGroup->id)->values();
                $orderedGroups->splice($position, 0, [$productGroup]);
                $orderedGroups->each(fn ($group, $index) => $group->update(['sort_order' => $index]));
            }

            $validated['sort_order'] = $position;
            $productGroup->update($validated);
        });

        return response()->json(['success' => true, 'message' => 'Product Group berhasil diperbarui.']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductGroup $productGroup)
    {
        DB::transaction(function () use ($productGroup) {
            $productId = $productGroup->product_id;
            $productGroup->delete();
            ProductGroup::query()->where('product_id', $productId)
                ->orderBy('sort_order')->orderBy('id')->lockForUpdate()->get()
                ->each(fn ($group, $index) => $group->update(['sort_order' => $index]));
        });

        return response()->json(['success' => true, 'message' => 'Product Group berhasil dihapus.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductGroup $productGroup)
    {
        //
    }
}

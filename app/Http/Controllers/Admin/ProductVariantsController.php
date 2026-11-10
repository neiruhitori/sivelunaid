<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductGroup;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductVariantsController extends Controller
{
    public function index()
    {
        return view('admin.product-variants.index', [
            'groups' => ProductGroup::query()->with('product:id,name')->orderBy('name')->get(),
        ]);
    }

    public function data()
    {
        $query = ProductVariant::query()
            ->join('product_groups', 'product_groups.id', '=', 'product_variants.product_group_id')
            ->select(['product_variants.*', 'product_groups.name as group_name']);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('price_display', function (ProductVariant $variant) {
                $price = 'Rp' . number_format((float) $variant->price, 0, ',', '.');
                if ($variant->discount_price === null) {
                    return '<span class="font-weight-bold">' . $price . '</span>';
                }

                $promo = 'Rp' . number_format((float) $variant->discount_price, 0, ',', '.');

                return '<span class="text-muted text-sm"><s>' . $price . '</s></span><br><span class="text-danger font-weight-bold">' . $promo . '</span>';
            })
            ->addColumn('discount_display', fn (ProductVariant $variant) => $variant->discount_price === null
                ? '<span class="text-muted">-</span>'
                : '<span class="badge badge-danger mr-1">Promo</span>Rp' . number_format((float) $variant->discount_price, 0, ',', '.'))
            ->addColumn('status', fn (ProductVariant $variant) => $variant->is_active
                ? '<span class="badge badge-success">Aktif</span>'
                : '<span class="badge badge-secondary">Nonaktif</span>')
            ->editColumn('created_at', fn (ProductVariant $variant) => $variant->created_at?->format('d/m/Y H:i'))
            ->addColumn('actions', fn (ProductVariant $variant) => '
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" data-action="edit" data-id="' . $variant->id . '" class="btn btn-outline-primary"><i class="fas fa-edit"></i> Edit</button>
                    <button type="button" data-action="delete" data-id="' . $variant->id . '" class="btn btn-outline-danger"><i class="fas fa-trash"></i> Hapus</button>
                </div>')
            ->rawColumns(['price_display', 'discount_display', 'status', 'actions'])
            ->make(true);
    }

    public function create()
    {
        return redirect()->route('admin.product-variants.index');
    }

    public function store(Request $request)
    {
        $validated = $this->validateVariants($request);
        $items = array_values($validated['variants']);
        unset($validated['variants'], $validated['sort_order_custom']);
        $customSortOrder = $request->boolean('sort_order_custom');

        DB::transaction(function () use ($validated, $items, $customSortOrder) {
            $variants = $this->orderedVariants((int) $validated['product_group_id']);
            $position = min($customSortOrder ? (int) $validated['sort_order'] : $variants->count(), $variants->count());
            $batch = collect($items)->map(fn ($item) => new ProductVariant(array_merge($validated, $item)));
            $variants->splice($position, 0, $batch->all());
            $variants->each(function (ProductVariant $variant, int $index) {
                $variant->sort_order = $index;
                $variant->save();
            });
        });

        return response()->json(['success' => true, 'message' => count($items) . ' Product Variant berhasil ditambahkan.']);
    }

    public function show(ProductVariant $productVariant)
    {
        return redirect()->route('admin.product-variants.index');
    }

    public function edit(ProductVariant $productVariant)
    {
        return redirect()->route('admin.product-variants.index');
    }

    public function update(Request $request, ProductVariant $productVariant)
    {
        $validated = $this->validateVariants($request);
        $items = array_values($validated['variants']);
        unset($validated['variants'], $validated['sort_order_custom']);
        $customSortOrder = $request->boolean('sort_order_custom');

        DB::transaction(function () use ($productVariant, $validated, $items, $customSortOrder) {
            $oldGroupId = (int) $productVariant->product_group_id;
            $newGroupId = (int) $validated['product_group_id'];
            $oldVariants = $this->orderedVariants($oldGroupId);
            $oldIndex = $oldVariants->search(fn ($variant) => $variant->id === $productVariant->id);

            if ($newGroupId !== $oldGroupId) {
                $oldVariants->reject(fn ($variant) => $variant->id === $productVariant->id)->values()
                    ->each(fn ($variant, $index) => $variant->update(['sort_order' => $index]));
                $siblings = $this->orderedVariants($newGroupId);
                $position = min($customSortOrder ? (int) $validated['sort_order'] : $siblings->count(), $siblings->count());
            } else {
                $siblings = $oldVariants->reject(fn ($variant) => $variant->id === $productVariant->id)->values();
                $position = min($customSortOrder ? (int) $validated['sort_order'] : $oldIndex, $siblings->count());
            }

            $batch = collect($items)->map(fn ($item, $index) => $index === 0
                ? $productVariant
                : new ProductVariant());
            $siblings->splice($position, 0, $batch->all());

            foreach ($siblings as $index => $variant) {
                if ($variant->id === $productVariant->id) {
                    $variant->fill(array_merge($validated, $items[0], ['sort_order' => $index]))->save();
                } elseif (!$variant->exists) {
                    $variant->fill(array_merge($validated, $items[$index - $position], ['sort_order' => $index]))->save();
                } else {
                    $variant->update(['sort_order' => $index]);
                }
            }
        });

        return response()->json(['success' => true, 'message' => count($items) . ' Product Variant berhasil disimpan.']);
    }

    public function destroy(ProductVariant $productVariant)
    {
        DB::transaction(function () use ($productVariant) {
            $groupId = (int) $productVariant->product_group_id;
            $productVariant->delete();
            $this->orderedVariants($groupId)->each(fn ($variant, $index) => $variant->update(['sort_order' => $index]));
        });

        return response()->json(['success' => true, 'message' => 'Product Variant berhasil dihapus.']);
    }

    private function validateVariants(Request $request): array
    {
        return $request->validate([
            'product_group_id' => ['required', 'integer', 'exists:product_groups,id'],
            'variants' => ['required', 'array', 'min:1', 'max:50'],
            'variants.*.name' => ['required', 'string', 'max:150'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.discount_price' => ['nullable', 'numeric', 'min:0', 'lte:variants.*.price'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'sort_order_custom' => ['nullable', 'boolean'],
        ]);
    }

    private function orderedVariants(int $groupId)
    {
        return ProductVariant::query()->where('product_group_id', $groupId)
            ->orderBy('sort_order')->orderBy('id')->lockForUpdate()->get();
    }
}

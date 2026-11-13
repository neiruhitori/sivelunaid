<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductGroup;
use App\Models\ProductGroupOption;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ProductVariantsController extends Controller
{
    public function index()
    {
        return view('admin.product-variants.index', [
            'groups' => ProductGroup::query()->with(['product:id,name', 'options:id,product_group_id,name'])->orderBy('name')->get(),
        ]);
    }

    public function data()
    {
        $query = ProductVariant::query()
            ->join('product_groups', 'product_groups.id', '=', 'product_variants.product_group_id')
            ->leftJoin('product_group_options', 'product_group_options.id', '=', 'product_variants.product_group_option_id')
            ->select(['product_variants.*', 'product_groups.name as group_name', 'product_group_options.name as option_name']);

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

    public function groupItems(Request $request, ProductGroup $productGroup)
    {
        $optionId = $this->validatedOptionId($request, $productGroup);
        $variants = $this->orderedVariants((int) $productGroup->id, $optionId);

        return response()->json([
            'product_group_id' => $productGroup->id,
            'product_name' => $productGroup->product?->name,
            'group_name' => $productGroup->name,
            'option_id' => $optionId,
            'option_name' => $optionId ? ProductGroupOption::query()->whereKey($optionId)->value('name') : null,
            'variants' => $variants->map(fn (ProductVariant $variant) => [
                'id' => $variant->id,
                'name' => $variant->name,
                'description' => $variant->description,
                'price' => $variant->price,
                'discount_price' => $variant->discount_price,
                'sort_order' => $variant->sort_order,
                'is_active' => $variant->is_active,
            ])->values(),
        ]);
    }

    public function updateGroupItems(Request $request, ProductGroup $productGroup)
    {
        $optionId = $this->validatedOptionId($request, $productGroup);
        $validated = $request->validate([
            'variants' => ['required', 'array', 'min:1', 'max:50'],
            'variants.*.id' => ['required', 'integer', 'distinct', 'exists:product_variants,id'],
            'variants.*.name' => ['required', 'string', 'max:150'],
            'variants.*.description' => ['nullable', 'string', 'max:255'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.discount_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.sort_order' => ['required', 'integer', 'min:0', 'distinct'],
            'variants.*.is_active' => ['required', 'boolean'],
        ]);

        DB::transaction(function () use ($productGroup, $optionId, $validated) {
            $existing = $this->orderedVariants((int) $productGroup->id, $optionId);
            $submittedIds = collect($validated['variants'])->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all();
            $existingIds = $existing->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all();

            if ($submittedIds !== $existingIds) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'variants' => 'Daftar variant berubah. Tutup modal lalu buka kembali sebelum menyimpan.',
                ]);
            }

            $items = collect($validated['variants'])->values()
                ->sortBy(fn ($item) => (int) $item['sort_order'])
                ->values();
            $variantsById = $existing->keyBy('id');

            foreach ($items as $sortOrder => $item) {
                $variant = $variantsById->get((int) $item['id']);
                $discount = $item['discount_price'] ?? null;

                if ($discount !== null && (float) $discount > (float) $item['price']) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        "variants.{$sortOrder}.discount_price" => 'Harga promo tidak boleh melebihi harga normal.',
                    ]);
                }

                $variant->update([
                    'name' => $item['name'],
                    'description' => $item['description'] ?? null,
                    'price' => $item['price'],
                    'discount_price' => $discount,
                    'sort_order' => $sortOrder,
                    'is_active' => $item['is_active'],
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => count($validated['variants']) . ' Product Variant berhasil diperbarui.',
        ]);
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
        $optionId = ! empty($validated['product_group_option_id']) ? (int) $validated['product_group_option_id'] : null;

        DB::transaction(function () use ($validated, $items, $customSortOrder, $optionId) {
            $variants = $this->orderedVariants((int) $validated['product_group_id'], $optionId);
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
        $newOptionId = ! empty($validated['product_group_option_id']) ? (int) $validated['product_group_option_id'] : null;

        DB::transaction(function () use ($productVariant, $validated, $items, $customSortOrder, $newOptionId) {
            $oldGroupId = (int) $productVariant->product_group_id;
            $newGroupId = (int) $validated['product_group_id'];
            $oldOptionId = $productVariant->product_group_option_id ? (int) $productVariant->product_group_option_id : null;
            $oldVariants = $this->orderedVariants($oldGroupId, $oldOptionId);
            $oldIndex = $oldVariants->search(fn ($variant) => $variant->id === $productVariant->id);
            $scopeChanged = $newGroupId !== $oldGroupId || $newOptionId !== $oldOptionId;

            if ($scopeChanged) {
                $oldVariants->reject(fn ($variant) => $variant->id === $productVariant->id)->values()
                    ->each(fn ($variant, $index) => $variant->update(['sort_order' => $index]));
                $siblings = $this->orderedVariants($newGroupId, $newOptionId);
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
            $optionId = $productVariant->product_group_option_id ? (int) $productVariant->product_group_option_id : null;
            $productVariant->delete();
            $this->orderedVariants($groupId, $optionId)->each(fn ($variant, $index) => $variant->update(['sort_order' => $index]));
        });

        return response()->json(['success' => true, 'message' => 'Product Variant berhasil dihapus.']);
    }

    private function validateVariants(Request $request): array
    {
        $validated = $request->validate([
            'product_group_id' => ['required', 'integer', 'exists:product_groups,id'],
            'product_group_option_id' => ['nullable', 'integer', 'exists:product_group_options,id'],
            'variants' => ['required', 'array', 'min:1', 'max:50'],
            'variants.*.name' => ['required', 'string', 'max:150'],
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.discount_price' => ['nullable', 'numeric', 'min:0', 'lte:variants.*.price'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'sort_order_custom' => ['nullable', 'boolean'],
        ]);

        $group = ProductGroup::query()->findOrFail($validated['product_group_id']);
        $optionId = $validated['product_group_option_id'] ?? null;
        if ($group->has_options && ! $optionId) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'product_group_option_id' => 'Pilih subpilihan untuk Product Group ini.',
            ]);
        }
        if ($optionId) {
            $option = ProductGroupOption::query()->findOrFail($optionId);
            if (! $group->has_options || (int) $option->product_group_id !== (int) $group->id) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'product_group_option_id' => 'Subpilihan tidak sesuai dengan Product Group.',
                ]);
            }
        }

        return $validated;
    }

    private function validatedOptionId(Request $request, ProductGroup $productGroup): ?int
    {
        $validated = $request->validate([
            'product_group_option_id' => ['nullable', 'integer', 'exists:product_group_options,id'],
        ]);
        $optionId = isset($validated['product_group_option_id']) ? (int) $validated['product_group_option_id'] : null;

        if ($productGroup->has_options && $optionId === null) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'product_group_option_id' => 'Pilih subpilihan untuk Product Group ini.',
            ]);
        }

        if ($optionId !== null) {
            $belongsToGroup = ProductGroupOption::query()->whereKey($optionId)
                ->where('product_group_id', $productGroup->id)->exists();

            if (! $productGroup->has_options || ! $belongsToGroup) {
                abort(404);
            }
        }

        return $optionId;
    }

    private function orderedVariants(int $groupId, ?int $optionId = null)
    {
        return ProductVariant::query()->where('product_group_id', $groupId)
            ->when($optionId === null, fn ($query) => $query->whereNull('product_group_option_id'),
                fn ($query) => $query->where('product_group_option_id', $optionId))
            ->orderBy('sort_order')->orderBy('id')->lockForUpdate()->get();
    }
}

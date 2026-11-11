<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductField;
use App\Models\ProductGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ProductFieldsController extends Controller
{
    public function index()
    {
        return view('admin.product-fields.index', [
            'groups' => ProductGroup::query()->with('product:id,name')->orderBy('name')->get(),
        ]);
    }

    public function data()
    {
        $query = ProductField::query()
            ->join('product_groups', 'product_groups.id', '=', 'product_fields.product_group_id')
            ->select(['product_fields.*', 'product_groups.name as group_name']);

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('type_display', fn (ProductField $field) => $field->type === 'backup_code' ? 'Backup Code' : ucfirst($field->type))
            ->addColumn('required_status', fn (ProductField $field) => $field->is_required
                ? '<span class="badge badge-danger">Ya</span>'
                : '<span class="badge badge-secondary">Tidak</span>')
            ->addColumn('placeholder_display', fn (ProductField $field) => $field->placeholder ?: '-')
            ->addColumn('actions', fn (ProductField $field) => '
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" data-action="edit" data-id="' . $field->id . '" class="btn btn-outline-primary"><i class="fas fa-edit"></i> Edit</button>
                    <button type="button" data-action="delete" data-id="' . $field->id . '" class="btn btn-outline-danger"><i class="fas fa-trash"></i> Hapus</button>
                </div>')
            ->rawColumns(['required_status', 'actions'])
            ->make(true);
    }

    public function create()
    {
        return redirect()->route('admin.product-fields.index');
    }

    public function store(Request $request)
    {
        $validated = $this->validateFields($request);
        $items = array_values($validated['fields']);
        unset($validated['fields'], $validated['sort_order_custom']);
        $customSortOrder = $request->boolean('sort_order_custom');

        DB::transaction(function () use ($validated, $items, $customSortOrder) {
            $fields = $this->orderedFields((int) $validated['product_group_id']);
            $position = min($customSortOrder ? (int) $validated['sort_order'] : $fields->count(), $fields->count());
            $keys = $this->uniqueKeys((int) $validated['product_group_id'], $items);
            $batch = collect($items)->map(fn ($item, $index) => new ProductField(array_merge(
                $validated,
                $item,
                ['field_key' => $keys[$index]],
            )));
            $fields->splice($position, 0, $batch->all());
            $fields->each(function (ProductField $field, int $index) {
                $field->sort_order = $index;
                $field->save();
            });
        });

        return response()->json(['success' => true, 'message' => count($items) . ' Product Field berhasil ditambahkan.']);
    }

    public function show(ProductField $productField)
    {
        return redirect()->route('admin.product-fields.index');
    }

    public function edit(ProductField $productField)
    {
        return redirect()->route('admin.product-fields.index');
    }

    public function update(Request $request, ProductField $productField)
    {
        $validated = $this->validateFields($request);
        $items = array_values($validated['fields']);
        unset($validated['fields'], $validated['sort_order_custom']);
        $customSortOrder = $request->boolean('sort_order_custom');

        DB::transaction(function () use ($productField, $validated, $items, $customSortOrder) {
            $oldGroupId = (int) $productField->product_group_id;
            $newGroupId = (int) $validated['product_group_id'];
            $oldFields = $this->orderedFields($oldGroupId);
            $oldIndex = $oldFields->search(fn ($field) => $field->id === $productField->id);

            if ($newGroupId !== $oldGroupId) {
                $oldFields->reject(fn ($field) => $field->id === $productField->id)->values()
                    ->each(fn ($field, $index) => $field->update(['sort_order' => $index]));
                $siblings = $this->orderedFields($newGroupId);
                $position = min($customSortOrder ? (int) $validated['sort_order'] : $siblings->count(), $siblings->count());
            } else {
                $siblings = $oldFields->reject(fn ($field) => $field->id === $productField->id)->values();
                $position = min($customSortOrder ? (int) $validated['sort_order'] : $oldIndex, $siblings->count());
            }

            $keys = $this->uniqueKeys($newGroupId, $items, $productField->id);
            $batch = collect($items)->map(fn ($item, $index) => $index === 0 ? $productField : new ProductField());
            $siblings->splice($position, 0, $batch->all());

            foreach ($siblings as $index => $field) {
                if ($field->id === $productField->id) {
                    $field->fill(array_merge($validated, $items[0], ['field_key' => $keys[0], 'sort_order' => $index]))->save();
                } elseif (!$field->exists) {
                    $itemIndex = $index - $position;
                    $field->fill(array_merge($validated, $items[$itemIndex], ['field_key' => $keys[$itemIndex], 'sort_order' => $index]))->save();
                } else {
                    $field->update(['sort_order' => $index]);
                }
            }
        });

        return response()->json(['success' => true, 'message' => count($items) . ' Product Field berhasil disimpan.']);
    }

    public function destroy(ProductField $productField)
    {
        DB::transaction(function () use ($productField) {
            $groupId = (int) $productField->product_group_id;
            $productField->delete();
            $this->orderedFields($groupId)->each(fn ($field, $index) => $field->update(['sort_order' => $index]));
        });

        return response()->json(['success' => true, 'message' => 'Product Field berhasil dihapus.']);
    }

    private function validateFields(Request $request): array
    {
        $validated = $request->validate([
            'product_group_id' => ['required', 'integer', 'exists:product_groups,id'],
            'fields' => ['required', 'array', 'min:1', 'max:50'],
            'fields.*.name' => ['required', 'string', 'max:100'],
            'fields.*.type' => ['required', 'in:text,number,email,password,backup_code'],
            'fields.*.placeholder' => ['nullable', 'string', 'max:255'],
            'fields.*.is_required' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'sort_order_custom' => ['nullable', 'boolean'],
        ]);

        foreach ($validated['fields'] as &$field) {
            if ($field['type'] === 'backup_code') {
                $field['is_required'] = false;
            }
        }
        unset($field);

        return $validated;
    }

    private function uniqueKeys(int $groupId, array $items, ?int $ignoreId = null): array
    {
        $reserved = [];

        foreach ($items as $index => $item) {
            $base = Str::slug($item['name'], '_') ?: 'field';
            $key = Str::limit($base, 100, '');
            $suffix = 2;

            while (in_array($key, $reserved, true) || ProductField::query()
                ->where('product_group_id', $groupId)
                ->where('field_key', $key)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()) {
                $tail = '_' . $suffix++;
                $key = Str::limit($base, 100 - strlen($tail), '') . $tail;
            }

            $reserved[] = $key;
            $items[$index]['field_key'] = $key;
        }

        return array_column($items, 'field_key');
    }

    private function orderedFields(int $groupId)
    {
        return ProductField::query()->where('product_group_id', $groupId)
            ->orderBy('sort_order')->orderBy('id')->lockForUpdate()->get();
    }
}

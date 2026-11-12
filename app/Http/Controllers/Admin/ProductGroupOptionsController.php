<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductGroup;
use App\Models\ProductGroupOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ProductGroupOptionsController extends Controller
{
    public function index(ProductGroup $productGroup): JsonResponse
    {
        abort_unless($productGroup->has_options, 404);

        return response()->json([
            'options' => $productGroup->options()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request, ProductGroup $productGroup): JsonResponse
    {
        abort_unless($productGroup->has_options, 404);
        $data = $this->validatedOption($request, $productGroup);
        $option = DB::transaction(function () use ($productGroup, $data) {
            $siblings = $productGroup->options()->lockForUpdate()->get();
            $position = min((int) $data['sort_order'], $siblings->count());
            $productGroup->options()->where('sort_order', '>=', $position)->increment('sort_order');
            $data['sort_order'] = $position;

            return $productGroup->options()->create($data);
        });

        return response()->json(['success' => true, 'option' => $option, 'message' => 'Subpilihan berhasil ditambahkan.']);
    }

    public function update(Request $request, ProductGroup $productGroup, ProductGroupOption $option): JsonResponse
    {
        abort_unless($productGroup->has_options && (int) $option->product_group_id === (int) $productGroup->id, 404);
        $data = $this->validatedOption($request, $productGroup, $option);
        DB::transaction(function () use ($productGroup, $option, $data) {
            $siblings = $productGroup->options()->lockForUpdate()->get();
            $oldPosition = (int) $option->sort_order;
            $newPosition = min((int) $data['sort_order'], max(0, $siblings->count() - 1));
            if ($newPosition < $oldPosition) {
                $productGroup->options()->where('id', '!=', $option->id)->whereBetween('sort_order', [$newPosition, $oldPosition - 1])->increment('sort_order');
            } elseif ($newPosition > $oldPosition) {
                $productGroup->options()->where('id', '!=', $option->id)->whereBetween('sort_order', [$oldPosition + 1, $newPosition])->decrement('sort_order');
            }
            $data['sort_order'] = $newPosition;
            $option->update($data);
        });

        return response()->json(['success' => true, 'option' => $option->fresh(), 'message' => 'Subpilihan berhasil diperbarui.']);
    }

    public function destroy(ProductGroup $productGroup, ProductGroupOption $option): JsonResponse
    {
        abort_unless((int) $option->product_group_id === (int) $productGroup->id, 404);
        if ($option->variants()->exists()) {
            return response()->json(['message' => 'Subpilihan ini masih memiliki Product Variant. Pindahkan atau hapus variant terlebih dahulu.'], 422);
        }

        $option->delete();
        $productGroup->options()->orderBy('sort_order')->orderBy('id')->get()
            ->each(fn ($item, $index) => $item->update(['sort_order' => $index]));

        return response()->json(['success' => true, 'message' => 'Subpilihan berhasil dihapus.']);
    }

    private function validatedOption(Request $request, ProductGroup $group, ?ProductGroupOption $option = null): array
    {
        $uniqueName = Rule::unique('product_group_options', 'name')
            ->where('product_group_id', $group->id);
        if ($option) {
            $uniqueName->ignore($option->id);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:150', $uniqueName],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);
    }
}

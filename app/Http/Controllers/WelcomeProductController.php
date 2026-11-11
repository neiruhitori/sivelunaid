<?php

namespace App\Http\Controllers;

use App\Models\ProductGroup;
use Illuminate\View\View;

class WelcomeProductController extends Controller
{
    public function show(ProductGroup $productGroup): View
    {
        abort_unless($productGroup->is_active && $productGroup->product?->is_active, 404);

        $productGroup->load([
            'product.category',
            'variants' => fn ($query) => $query->where('is_active', true)
                ->orderBy('sort_order')->orderBy('id'),
            'fields' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
        ]);

        $aboutItems = collect(preg_split('/\r\n|\r|\n|<br\s*\/?\s*>/i', $productGroup->description ?? '', -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($item) => preg_replace('/^\s*(?:(?:[-*•])\s+|\d+[.)]\s*)/u', '', trim($item)))
            ->filter()
            ->values();

        return view('welcome.robux', compact('productGroup', 'aboutItems'));
    }
}

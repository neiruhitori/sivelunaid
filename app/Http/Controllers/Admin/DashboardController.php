<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Dashboard;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard', [
            'categoryCount' => Category::count(),
            'productCount' => Product::count(),
            'productGroupCount' => ProductGroup::count(),
            'productVariantCount' => ProductVariant::count(),
            'orderCount' => Order::count(),
            'pendingOrderCount' => Order::where('status', 'pending_payment')->count(),
            'storefrontOpen' => SiteSetting::storefrontIsOpen(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Dashboard $dashboard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dashboard $dashboard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dashboard $dashboard)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dashboard $dashboard)
    {
        //
    }
}

<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\ProductGroupsController;
use App\Http\Controllers\Admin\ProductVariantsController;
use App\Http\Controllers\Admin\ProductFieldsController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Categories
        Route::resource('categories', CategoriesController::class);

        // Products
        Route::resource('products', ProductsController::class);

        // Product Groups
        Route::resource('product-groups', ProductGroupsController::class);

        // Product Variants / Price List
        Route::resource('product-variants', ProductVariantsController::class);

        // Product Fields
        Route::resource('product-fields', ProductFieldsController::class);
    });


<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\ProfileController;
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
    return view('welcome.index');
})->name('welcome.index');

Route::view('/mobile-legends', 'welcome.mobile-legends')->name('welcome.mobile-legends');
Route::view('/roblox', 'welcome.roblox')->name('welcome.roblox');
Route::view('/roblox-vilog', 'welcome.roblox-vilog')->name('welcome.roblox-vilog');
Route::view('/roblox-gig', 'welcome.roblox-gig')->name('welcome.roblox-gig');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

// Dashboard Breeze
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Categories
        Route::get('categories/data', [CategoriesController::class, 'data'])
            ->name('categories.data');
        Route::resource('categories', CategoriesController::class);

        // Products
        Route::get('products/data', [ProductsController::class, 'data'])
            ->name('products.data');
        Route::resource('products', ProductsController::class)
            ->only(['index', 'store', 'update', 'destroy']);

        // Product Groups
        Route::get('product-groups/data', [ProductGroupsController::class, 'data'])
            ->name('product-groups.data');
        Route::resource('product-groups', ProductGroupsController::class);

        // Product Variants
        Route::get('product-variants/data', [ProductVariantsController::class, 'data'])
            ->name('product-variants.data');
        Route::resource('product-variants', ProductVariantsController::class);

        // Product Fields
        Route::get('product-fields/data', [ProductFieldsController::class, 'data'])
            ->name('product-fields.data');
        Route::resource('product-fields', ProductFieldsController::class);
    });

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';


<?php

use Illuminate\Support\Facades\Route;
use App\Models\Category;
use App\Models\Product;
use App\Http\Controllers\WelcomeProductController;
use App\Http\Controllers\Admin\StorefrontController;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\ProductGroupsController;
use App\Http\Controllers\Admin\ProductVariantsController;
use App\Http\Controllers\Admin\ProductFieldsController;
use App\Http\Controllers\Admin\ProductGroupOptionsController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::middleware('storefront.open')->group(function () {
Route::get('/', function () {
    $categories = Category::query()
        ->where('is_active', true)
        ->with(['products' => function ($query) {
            $query->where('is_active', true)
                ->with(['groups' => function ($groupQuery) {
                    $groupQuery->where('is_active', true)
                        ->select(['id', 'product_id', 'name', 'description', 'sort_order'])
                        ->orderBy('sort_order')
                        ->orderBy('id');
                }])
                ->select(['id', 'category_id', 'name', 'slug', 'description', 'image', 'sort_order'])
                ->orderBy('sort_order')
                ->orderBy('id');
        }])
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();

    return view('welcome.index', compact('categories'));
})->name('welcome.index');

Route::get('/robux/{productGroup}', [WelcomeProductController::class, 'show'])
    ->whereNumber('productGroup')
    ->name('welcome.product-group');

// Backward-compatible links send visitors to the first active group for each game.
$redirectToFirstProductGroup = static function (string $slug) {
    $group = Product::query()->where('slug', $slug)->where('is_active', true)
        ->with(['groups' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')->orderBy('id')])
        ->first()?->groups->first();

    return $group ? redirect()->route('welcome.product-group', $group) : redirect()->route('welcome.index');
};

Route::get('/mobile-legends', fn () => $redirectToFirstProductGroup('mobile-legends'))->name('welcome.mobile-legends');
Route::get('/roblox', fn () => $redirectToFirstProductGroup('roblox'))->name('welcome.roblox');
Route::get('/roblox-vilog', fn () => $redirectToFirstProductGroup('roblox'))->name('welcome.roblox-vilog');
Route::get('/roblox-gig', fn () => $redirectToFirstProductGroup('roblox'))->name('welcome.roblox-gig');
});

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

        Route::post('/storefront/status', [StorefrontController::class, 'update'])
            ->name('storefront.status');

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
        Route::get('product-groups/{productGroup}/options', [ProductGroupOptionsController::class, 'index'])
            ->whereNumber('productGroup')->name('product-groups.options.index');
        Route::post('product-groups/{productGroup}/options', [ProductGroupOptionsController::class, 'store'])
            ->whereNumber('productGroup')->name('product-groups.options.store');
        Route::put('product-groups/{productGroup}/options/{option}', [ProductGroupOptionsController::class, 'update'])
            ->whereNumber('productGroup')->whereNumber('option')->name('product-groups.options.update');
        Route::delete('product-groups/{productGroup}/options/{option}', [ProductGroupOptionsController::class, 'destroy'])
            ->whereNumber('productGroup')->whereNumber('option')->name('product-groups.options.destroy');
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

<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\NewsCategoryController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductBrandController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::fallback(function () {
    return Inertia::render('404/Index');
});
// Client

// Admin
Route::prefix('@admin')->group(function () {
    // Auth
    Route::get('login', [AuthController::class, 'index'])->name('admin.login');
    Route::post('login', [AuthController::class, 'login'])->name('admin.login.post')->middleware('throttle:10,1');
    Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');
    // Dashboard
    Route::middleware(['isAdmin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
        // user
        Route::get('users', [UserController::class, 'index'])->name('admin.users');
        Route::post('users', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        // news
        Route::get('news', [NewsController::class, 'index'])->name('admin.news');
        Route::post('news/store', [NewsController::class, 'store'])->name('admin.news.store');
        Route::post('news/update/{id}', [NewsController::class, 'update'])->name('admin.news.update');
        Route::get('news/content/{id}', [NewsController::class, 'getContent'])->name('admin.news.content');
        Route::get('news/tags/{id}', [NewsController::class, 'getTags'])->name('admin.news.tags');
        Route::post('news/content/{id}', [NewsController::class, 'updateContent'])->name('admin.news.content.update');
        Route::delete('news/{id}', [NewsController::class, 'destroy'])->name('admin.news.destroy');
        // categories
        Route::get('news-categories', [NewsCategoryController::class, 'index'])->name('admin.news.categories');
        Route::post('news-categories/store', [NewsCategoryController::class, 'store'])->name('admin.news.categories.store');
        Route::post('news-categories/update/{id}', [NewsCategoryController::class, 'update'])->name('admin.news.categories.update');
        Route::delete('news-categories/{id}', [NewsCategoryController::class, 'destroy'])->name('admin.news.categories.destroy');
        // products
        Route::get('products', [ProductController::class, 'index'])->name('admin.products');
        Route::get('products/create', [ProductController::class, 'create'])->name('admin.products.create');
        Route::post('products/store', [ProductController::class, 'store'])->name('admin.products.store');
        Route::get('products/edit/{id}', [ProductController::class, 'edit'])->name('admin.products.edit');
        Route::post('products/update/{id}', [ProductController::class, 'update'])->name('admin.products.update');
        Route::delete('products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
        // product categories
        Route::get('product-categories', [ProductCategoryController::class, 'index'])->name('admin.product.categories');
        Route::post('product-categories/store', [ProductCategoryController::class, 'store'])->name('admin.product.categories.store');
        Route::post('product-categories/update/{id}', [ProductCategoryController::class, 'update'])->name('admin.product.categories.update');
        Route::delete('product-categories/{id}', [ProductCategoryController::class, 'destroy'])->name('admin.product.categories.destroy');
        // product brands
        Route::get('product-brands', [ProductBrandController::class, 'index'])->name('admin.product.brands');
        Route::post('product-brands/store', [ProductBrandController::class, 'store'])->name('admin.product.brands.store');
        Route::post('product-brands/update/{id}', [ProductBrandController::class, 'update'])->name('admin.product.brands.update');
        Route::delete('product-brands/{id}', [ProductBrandController::class, 'destroy'])->name('admin.product.brands.destroy');
        // order
        Route::get('orders', [OrderController::class, 'index'])->name('admin.orders');
        // Logs
        Route::get('admin-logs', [LogController::class, 'index'])->name('admin.logs');
        Route::delete('admin-logs/{id}', [LogController::class, 'destroy'])->name('admin.logs.destroy');
        // settings
        Route::get('settings', [SettingsController::class, 'index'])->name('admin.settings');
        Route::post('settings/update', [SettingsController::class, 'update'])->name('admin.settings.update');
    });
});

require __DIR__ . '/auth.php';

<?php

use App\Http\Controllers\Client\ContactController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\NewsController;
use App\Http\Controllers\Client\ProductController;

// Client
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/san-pham', [ProductController::class, 'index'])->name('products');
Route::get('/san-pham/{slug}', [ProductController::class, 'detail'])->name('products.detail');
Route::get('/danh-muc/{slug}', [ProductController::class, 'productCategory'])->name('products.category');
Route::get('/lien-he', [ContactController::class, 'index'])->name('contact');
Route::get('/tin-tuc', [NewsController::class, 'index'])->name('news');
Route::get('/tin-tuc/{slug}', [NewsController::class, 'detail'])->name('news.detail');
Route::get('/tin-tuc/danh-muc/{slug}', [NewsController::class, 'category'])->name('news.category');

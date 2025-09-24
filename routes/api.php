<?php

use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\Client\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// admin
Route::post('/admin/products/upload', [UploadController::class, 'store']);
Route::delete('/admin/products/upload/delete/{id}', [UploadController::class, 'destroy'])->name('admin.image.delete');
// client
Route::get('/products/search', [ProductController::class, 'search']);

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\ProductController;
use App\Http\Controllers\API\V1\ProductVariantController;

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


Route::prefix('v1')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/search', [ProductController::class, 'search']);
    Route::get('search_product_via_meilisearch', [ProductController::class, 'searchProductViaMeilisearch']);
    Route::post('products', [ProductController::class, 'store']);
    Route::get('products/{name}', [ProductController::class, 'show']);
    Route::put('products/{name}', [ProductController::class, 'update']);
    Route::delete('products/{name}', [ProductController::class, 'destroy']);

    Route::get('product_variants', [ProductVariantController::class, 'index']);
    Route::post('product_variants', [ProductVariantController::class, 'store']);
    Route::get('product_variants/{name}', [ProductVariantController::class, 'show']);
    Route::put('product_variants/{name}', [ProductVariantController::class, 'update']);
    Route::delete('product_variants/{name}', [ProductVariantController::class, 'destroy']);
});

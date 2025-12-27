<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\Cms\BannerController;
use App\Http\Controllers\Api\Cms\InternalPageController;
use App\Http\Controllers\Api\Cms\LegalTextController;
use App\Http\Controllers\Api\Cms\SectionController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\SubcategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::put('me', [AuthController::class, 'update']);
    });
});

Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('subcategories', SubcategoryController::class)->only(['index', 'show']);

Route::apiResource('sections', SectionController::class)->only(['index', 'show']);
Route::apiResource('banners', BannerController::class)->only(['index', 'show']);
Route::apiResource('internal-pages', InternalPageController::class)->only(['index', 'show']);
Route::apiResource('legal-texts', LegalTextController::class)->only(['index', 'show']);

Route::apiResource('products', ProductController::class)->only(['index', 'show']);

Route::get('orders', [OrderController::class, 'index'])->middleware('auth:sanctum');
Route::get('orders/{order}', [OrderController::class, 'show'])->middleware('auth:sanctum');
Route::post('orders', [OrderController::class, 'store'])->middleware('auth:sanctum');

Route::apiResource('restaurants', RestaurantController::class)->only(['index', 'show']);

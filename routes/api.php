<?php

use App\Http\Controllers\Api\OrderController as ApiOrderController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {

    // ── Public endpoints ──────────────────────────────────
    Route::get('/products',           [ApiProductController::class, 'index'])->name('products.index');
    Route::get('/products/{uuid}',    [ApiProductController::class, 'show'])->name('products.show');

    // ── Authenticated endpoints ───────────────────────────
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/orders',         [ApiOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{uuid}',  [ApiOrderController::class, 'show'])->name('orders.show');
    });
});
<?php

use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {

        Route::get('/', fn () => redirect()->route('admin.orders.index'));

        // ── Products ──────────────────────────────────────

        Route::resource('products', AdminProductController::class);

        // ── Orders ────────────────────────────────────────

        Route::prefix('orders')->name('orders.')->group(function () {

            Route::get('/', [AdminOrderController::class, 'index'])
                ->name('index');

            Route::get('/{order:uuid}', [AdminOrderController::class, 'show'])
                ->name('show');

            Route::patch('/{order:uuid}/status', [AdminOrderController::class, 'updateStatus'])
                ->name('updateStatus');
        });
    });
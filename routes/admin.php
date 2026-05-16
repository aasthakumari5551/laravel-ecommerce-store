<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\OrderController  as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReviewController  as AdminReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
     ->name('admin.')
     ->middleware(['auth', 'admin'])
     ->group(function () {

    Route::get('/', fn () => redirect()->route('admin.analytics.dashboard'));

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'dashboard'])
         ->name('analytics.dashboard');

    // Products
    Route::resource(
    'products',
    AdminProductController::class
)->parameters([
    'products' => 'product:uuid'
]);

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/',                       [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{order:uuid}',           [AdminOrderController::class, 'show'])->name('show');
        Route::patch('/{order:uuid}/status',  [AdminOrderController::class, 'updateStatus'])->name('updateStatus');
    });

    // Coupons
    Route::resource('coupons', AdminCouponController::class);

    // Reviews
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/',                    [AdminReviewController::class, 'index'])->name('index');
        Route::patch('/{review}/moderate', [AdminReviewController::class, 'moderate'])->name('moderate');
    });
});
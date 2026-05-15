<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Shop\ProductController as ShopProductController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// Shop — product catalog (public)
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/products',
        [ShopProductController::class, 'index'])->name('products.index');
    Route::get('/products/suggestions',
        [ShopProductController::class, 'suggestions'])->name('products.suggestions');
    Route::get('/products/{product:uuid}',
        [ShopProductController::class, 'show'])->name('products.show');
    Route::get('/compare',
        fn (\Illuminate\Http\Request $req) => view('shop.compare.index', [
            'products' => \App\Models\Product::whereIn('uuid',
                    array_slice(explode(',', $req->input('ids', '')), 0, 3))
                ->with(['primaryImage', 'category'])->get(),
        ])
    )->name('compare');
});

// ── Cart (guests + auth) ──────────────────────────────────
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/',              [CartController::class, 'index'])->name('index');
    Route::post('/',             [CartController::class, 'store'])->name('store');
    Route::patch('/{cartItem}',  [CartController::class, 'update'])->name('update');
    Route::delete('/{cartItem}', [CartController::class, 'destroy'])->name('destroy');
    Route::delete('/',           [CartController::class, 'clear'])->name('clear');
});

// ── Auth-required ─────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Wishlist
    Route::prefix('wishlist')->name('wishlist.')->group(function () {
        Route::get('/',               [WishlistController::class, 'index'])->name('index');
        Route::post('/toggle',        [WishlistController::class, 'toggle'])->name('toggle');
        Route::post('/clear',         [WishlistController::class, 'clear'])->name('clear');
        Route::delete('/{productId}', [WishlistController::class, 'destroy'])->name('destroy');
    });

    // Coupons
    Route::prefix('coupons')->name('coupons.')->group(function () {
        Route::post('/apply',  [CouponController::class, 'apply'])->name('apply');
        Route::post('/remove', [CouponController::class, 'remove'])->name('remove');
    });

    // Checkout
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/',              [CheckoutController::class, 'index'])->name('index');
        Route::post('/initiate',     [CheckoutController::class, 'initiate'])->name('initiate');
        Route::get('/demo-payment',  [CheckoutController::class, 'demoPayment'])->name('demo-payment');
        Route::post('/demo/success', [CheckoutController::class, 'simulateSuccess'])->name('demo.success');
        Route::post('/demo/failure', [CheckoutController::class, 'simulateFailure'])->name('demo.failure');
    });

    // Orders
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/',                    [OrderController::class, 'index'])->name('index');
        Route::get('/{order:uuid}',        [OrderController::class, 'show'])->name('show');
        Route::post('/{order:uuid}/cancel',[OrderController::class, 'cancel'])->name('cancel');
    });

    // Addresses
    Route::prefix('addresses')->name('addresses.')->group(function () {
        Route::get('/',             [AddressController::class, 'index'])->name('index');
        Route::post('/',            [AddressController::class, 'store'])->name('store');
        Route::put('/{address}',    [AddressController::class, 'update'])->name('update');
        Route::delete('/{address}', [AddressController::class, 'destroy'])->name('destroy');
    });

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',           [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/',         [ProfileController::class, 'update'])->name('update');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('password');
    });

    // Reviews
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::post('/products/{product:uuid}', [ReviewController::class, 'store'])->name('store');
        Route::patch('/{review}',               [ReviewController::class, 'update'])->name('update');
        Route::delete('/{review}',              [ReviewController::class, 'destroy'])->name('destroy');
    });
});

require __DIR__ . '/auth.php';
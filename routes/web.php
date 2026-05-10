<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CheckoutController;

// ── Home ───────────────────────────────────────────────────

Route::get('/', [ProductController::class, 'index'])->name('home');

// ── Dashboard ──────────────────────────────────────────────

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ── Profile ────────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// ── Products ───────────────────────────────────────────────

Route::prefix('products')->name('products.')->group(function () {

    Route::get('/', [ProductController::class, 'index'])
        ->name('index');

    Route::get('/create', [ProductController::class, 'create'])
        ->name('create');

    Route::post('/', [ProductController::class, 'store'])
        ->name('store');

    Route::get('/{product}/edit', [ProductController::class, 'edit'])
        ->name('edit');

    Route::put('/{product}', [ProductController::class, 'update'])
        ->name('update');

    Route::delete('/{product}', [ProductController::class, 'destroy'])
        ->name('destroy');
});

// ── Cart (Guests + Auth) ──────────────────────────────────

Route::prefix('cart')->name('cart.')->group(function () {

    Route::get('/', [CartController::class, 'index'])
        ->name('index');

    Route::post('/', [CartController::class, 'store'])
        ->name('store');

    Route::patch('/{cartItem}', [CartController::class, 'update'])
        ->name('update');

    Route::delete('/{cartItem}', [CartController::class, 'destroy'])
        ->name('destroy');

    Route::delete('/', [CartController::class, 'clear'])
        ->name('clear');
});

// ── Wishlist (Auth Only) ──────────────────────────────────

Route::prefix('wishlist')
    ->name('wishlist.')
    ->middleware('auth')
    ->group(function () {

        Route::get('/', [WishlistController::class, 'index'])
            ->name('index');

        Route::post('/toggle', [WishlistController::class, 'toggle'])
            ->name('toggle');

        Route::delete('/{productId}', [WishlistController::class, 'destroy'])
            ->name('destroy');
    });

// ── Checkout + Orders + Addresses ─────────────────────────

Route::middleware('auth')->group(function () {

    // ── Checkout ──────────────────────────────────────────

    Route::prefix('checkout')->name('checkout.')->group(function () {

        Route::get('/', [CheckoutController::class, 'index'])
            ->name('index');

        Route::post('/initiate', [CheckoutController::class, 'initiate'])
            ->name('initiate');

        // Demo payment routes

        Route::get('/demo-payment', [CheckoutController::class, 'demoPayment'])
            ->name('demo-payment');

        Route::post('/demo/success', [CheckoutController::class, 'simulateSuccess'])
            ->name('demo.success');

        Route::post('/demo/failure', [CheckoutController::class, 'simulateFailure'])
            ->name('demo.failure');
    });

    // ── Customer Orders ──────────────────────────────────

    Route::prefix('orders')->name('orders.')->group(function () {

        Route::get('/', [OrderController::class, 'index'])
            ->name('index');

        Route::get('/{order}', [OrderController::class, 'show'])
            ->name('show');
    });

    // ── Addresses ────────────────────────────────────────

    Route::prefix('addresses')->name('addresses.')->group(function () {

        Route::get('/', [AddressController::class, 'index'])
            ->name('index');

        Route::post('/', [AddressController::class, 'store'])
            ->name('store');

        Route::put('/{address}', [AddressController::class, 'update'])
            ->name('update');

        Route::delete('/{address}', [AddressController::class, 'destroy'])
            ->name('destroy');
    });
});

// ── Auth Routes ────────────────────────────────────────────

require __DIR__.'/auth.php';
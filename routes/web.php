<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WishlistController;

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

// ── Checkout ───────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    Route::get('/checkout', [OrderController::class, 'checkout'])
        ->name('checkout');
});

// ── Auth Routes ────────────────────────────────────────────

require __DIR__.'/auth.php';
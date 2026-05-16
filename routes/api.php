<?php

use App\Http\Controllers\Api\OrderController as ApiOrderController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::prefix('v1')->name('api.v1.')->group(function () {

    // ── Public endpoints ──────────────────────────────────
    Route::get('/products',           [ApiProductController::class, 'index'])->name('products.index');
    Route::get('/products/{uuid}',    [ApiProductController::class, 'show'])->name('products.show');

    // ── Authenticated endpoints ───────────────────────────
    Route::middleware(['web', 'auth'])->group(function () {
        Route::get('/orders',         [ApiOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{uuid}',  [ApiOrderController::class, 'show'])->name('orders.show');
    });
});

Route::middleware('web')->group(function () {
    // Cart drawer data endpoint
    Route::get('/cart', function () {
        $cartService = app(\App\Services\CartService::class);
        $summary     = $cartService->summary();

        // Attach product data for drawer
        $items = $summary['items']->load(['product.primaryImage']);
        $itemsData = $items->map(fn ($item) => [
            'id'           => $item->id,
            'product_name' => $item->product->name,
            'quantity'     => $item->quantity,
            'unit_price'   => (float) $item->unit_price,
            'product'      => [
                'uuid'              => $item->product->uuid,
                'primary_image_url' => $item->product->primaryImage?->url,
            ],
        ]);

        return response()->json([
            'items'       => $itemsData,
            'subtotal'    => $summary['subtotal'],
            'total_items' => $summary['total_items'],
        ]);
    });
});

Route::middleware(['web', 'auth'])->prefix('notifications')->group(function () {

    Route::get('/', function () {
        $user = Auth::user();
        return response()->json([
            'notifications' => $user->notifications()->latest()->limit(15)->get(),
            'unread'        => $user->unreadNotifications()->count(),
        ]);
    });

    Route::post('/{id}/read', function (string $id) {
        Auth::user()->notifications()->where('id', $id)->first()?->markAsRead();
        return response()->json(['ok' => true]);
    });

    Route::post('/read-all', function () {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    });
});
<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\StoreCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index()
    {
        $cart = $this->cartService->summary();
        return view('shop.cart.index', compact('cart'));
    }

    public function store(StoreCartItemRequest $request): RedirectResponse|JsonResponse
    {
        try {
            $this->cartService->add(
                (int) $request->validated('product_id'),
                (int) $request->validated('quantity', 1),
            );
        } catch (\RuntimeException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            return back()->withErrors(['stock' => $e->getMessage()]);
        }

        if ($request->wantsJson()) {
            $summary = $this->cartService->summary();
            return response()->json([
                'message'     => 'Added to cart',
                'cart_count'  => $summary['total_items'],
                'subtotal'    => $summary['subtotal'],
            ]);
        }

        return back()->with('success', 'Item added to cart.');
    }

    public function update(UpdateCartItemRequest $request, int $cartItemId): RedirectResponse|JsonResponse
    {
        try {
            $this->cartService->update($cartItemId, (int) $request->validated('quantity'));
        } catch (\RuntimeException $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
            return back()->withErrors(['stock' => $e->getMessage()]);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Cart updated']);
        }

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(int $cartItemId): RedirectResponse|JsonResponse
    {
        $this->cartService->remove($cartItemId);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Item removed']);
        }

        return back()->with('success', 'Item removed.');
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clear();
        return back()->with('success', 'Cart cleared.');
    }
}
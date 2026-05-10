<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\StoreCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index()
    {
        $summary = $this->cartService->summary();
        return view('shop.cart.index', $summary);
    }

    public function store(StoreCartItemRequest $request): RedirectResponse
    {
        try {
            $this->cartService->add(
                $request->validated('product_id'),
                $request->validated('quantity'),
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['stock' => $e->getMessage()]);
        }

        return back()->with('success', 'Item added to cart.');
    }

    public function update(UpdateCartItemRequest $request, int $cartItemId): RedirectResponse
    {
        try {
            $this->cartService->update($cartItemId, $request->validated('quantity'));
        } catch (\RuntimeException $e) {
            return back()->withErrors(['stock' => $e->getMessage()]);
        }

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(int $cartItemId): RedirectResponse
    {
        $this->cartService->remove($cartItemId);
        return back()->with('success', 'Item removed from cart.');
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clear();
        return back()->with('success', 'Cart cleared.');
    }
}
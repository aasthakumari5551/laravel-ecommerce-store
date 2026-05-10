<?php

namespace App\Http\Controllers;

use App\Services\WishlistService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(private WishlistService $wishlistService)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $wishlist = $this->wishlistService->all();
        return view('shop.wishlist.index', compact('wishlist'));
    }

    public function toggle(Request $request): RedirectResponse
    {
        $request->validate(['product_id' => ['required', 'integer', 'exists:products,id']]);

        $added   = $this->wishlistService->toggle($request->product_id);
        $message = $added ? 'Added to wishlist.' : 'Removed from wishlist.';

        return back()->with('success', $message);
    }

    public function destroy(int $productId): RedirectResponse
    {
        $this->wishlistService->remove($productId);
        return back()->with('success', 'Removed from wishlist.');
    }
}
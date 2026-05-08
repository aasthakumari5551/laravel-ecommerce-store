<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Add product to cart
    public function add($id)
    {
        Cart::create([
            'user_id' => Auth::id(),
            'product_id' => $id,
            'quantity' => 1
        ]);

        return redirect('/cart');
    }

    // Show cart items
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())->get();

        return view('cart.index', compact('cartItems'));
    }

    // Increase quantity
public function increase($id)
{
    $cart = Cart::findOrFail($id);

    $cart->quantity += 1;

    $cart->save();

    return redirect('/cart');
}

// Decrease quantity
public function decrease($id)
{
    $cart = Cart::findOrFail($id);

    if($cart->quantity > 1)
    {
        $cart->quantity -= 1;

        $cart->save();
    }

    return redirect('/cart');
}

// Remove item from cart
public function remove($id)
{
    $cart = Cart::findOrFail($id);

    $cart->delete();

    return redirect('/cart');
}
}
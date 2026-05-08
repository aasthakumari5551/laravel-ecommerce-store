<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function checkout()
    {
        $cartItems = Cart::where('user_id', Auth::id())->get();

        $total = 0;

        foreach($cartItems as $item)
        {
            $total += $item->product->price * $item->quantity;

            // Reduce stock
            $product = Product::find($item->product_id);

            $product->stock -= $item->quantity;

            $product->save();
        }

        // Create order
        Order::create([
            'user_id' => Auth::id(),
            'total_price' => $total
        ]);

        // Clear cart
        Cart::where('user_id', Auth::id())->delete();

        return view('orders.success', compact('total'));
    }
}
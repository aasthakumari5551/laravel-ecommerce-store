<?php

namespace App\Http\Controllers;

use App\Http\Requests\Checkout\CancelOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService)
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = auth()->user()
                        ->orders()
                        ->with(['items'])
                        ->latest()
                        ->paginate(10);

        return view('shop.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $order->load(['items.product', 'statusHistories.changedBy']);

        return view('shop.orders.show', compact('order'));
    }

    public function cancel(CancelOrderRequest $request, Order $order): RedirectResponse
    {
        try {
            $this->orderService->cancelByCustomer($order);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['cancel' => $e->getMessage()]);
        }

        return back()->with('success', 'Your order has been cancelled.');
    }
}
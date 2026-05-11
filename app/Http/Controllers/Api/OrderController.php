<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()
                        ->orders()
                        ->withCount('items')
                        ->latest()
                        ->paginate(10);

        return OrderResource::collection($orders);
    }

    public function show(string $uuid)
    {
        $order = Order::where('uuid', $uuid)
                      ->where('user_id', auth()->id())
                      ->with(['items'])
                      ->firstOrFail();

        return new OrderResource($order);
    }
}
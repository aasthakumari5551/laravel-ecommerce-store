<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    // ── List with filters ─────────────────────────────────────

    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])
                      ->latest();

        // Filter by order status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order number or customer email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$search}%"));
            });
        }

        $orders        = $query->paginate(25)->withQueryString();
        $orderStatuses = OrderStatus::cases();
        $payStatuses   = PaymentStatus::cases();

        return view('admin.orders.index', compact('orders', 'orderStatuses', 'payStatuses'));
    }

    // ── Detail ────────────────────────────────────────────────

    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'statusHistories.changedBy']);

        $allowedTransitions = $order->status->allowedTransitions();

        return view('admin.orders.show', compact('order', 'allowedTransitions'));
    }

    // ── Status transition ─────────────────────────────────────

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        try {
            $this->orderService->transitionStatus(
                $order,
                OrderStatus::from($request->validated('status')),
                $request->validated('comment'),
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return back()->with('success', "Order {$order->number} updated to {$order->fresh()->status->label()}.");
    }
}
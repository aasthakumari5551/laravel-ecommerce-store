<?php

namespace App\Http\Controllers;

use App\Http\Requests\Checkout\InitiatePaymentRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\SimulatedPaymentService;
use Illuminate\Http\RedirectResponse;

class CheckoutController extends Controller
{
    public function __construct(
        private OrderService            $orderService,
        private CartService             $cartService,
        private SimulatedPaymentService $paymentService,
    ) {
        
    }

    // ── 1. Checkout page ──────────────────────────────────────

    public function index()
    {
        $cart = $this->cartService->summary();

        if ($cart['is_empty']) {
            return redirect()->route('cart.index')
                             ->with('error', 'Your cart is empty.');
        }

        $addresses = auth()->user()->addresses()->get();

        return view('shop.checkout.index', compact('addresses', 'cart'));
    }

    // ── 2. Initiate order + gateway order ─────────────────────

    public function initiate(InitiatePaymentRequest $request): RedirectResponse
    {
        try {
            $result = $this->orderService->initiate(
                $request->validated('address_id'),
                $request->validated('notes'),
            );
        } catch (\RuntimeException $e) {
            return back()->withErrors(['checkout' => $e->getMessage()]);
        }

        // Stash gateway data in session for demo payment screen
        session()->put([
            'demo_gateway_order_id' => $result['gateway_order']['id'],
            'demo_order_uuid'       => $result['order']->uuid,
            'demo_order_amount'     => $result['gateway_order']['amount'], // paise
        ]);

        return redirect()->route('checkout.demo-payment');
    }

    // ── 3. Demo payment screen ────────────────────────────────

    public function demoPayment()
    {
        $gatewayOrderId = session('demo_gateway_order_id');
        $orderUuid      = session('demo_order_uuid');
        $amountPaise    = session('demo_order_amount');

        if (! $gatewayOrderId || ! $orderUuid) {
            return redirect()->route('checkout.index');
        }

        $order  = Order::with('items.product')->where('uuid', $orderUuid)->firstOrFail();
        $amount = $amountPaise / 100;

        return view('shop.checkout.demo-payment', compact('order', 'gatewayOrderId', 'amount'));
    }

    // ── 4. Simulate success ───────────────────────────────────

    public function simulateSuccess(): RedirectResponse
    {
        $gatewayOrderId = session('demo_gateway_order_id');

        if (! $gatewayOrderId) {
            return redirect()->route('checkout.index')
                             ->withErrors(['checkout' => 'Session expired. Please try again.']);
        }

        $payload = $this->paymentService->simulateSuccess($gatewayOrderId);

        try {
            $order = $this->orderService->verifyAndConfirm(
                $payload['gateway_order_id'],
                $payload['gateway_payment_id'],
                $payload['gateway_signature'],
            );
        } catch (\RuntimeException $e) {
            $this->clearDemoSession();
            return redirect()->route('checkout.index')
                             ->withErrors(['payment' => $e->getMessage()]);
        }

        $this->clearDemoSession();

        return redirect()->route('orders.show', $order)
                         ->with('success', 'Payment successful! Your order has been placed.');
    }

    // ── 5. Simulate failure ───────────────────────────────────

    public function simulateFailure(): RedirectResponse
    {
        $gatewayOrderId = session('demo_gateway_order_id');

        if ($gatewayOrderId) {
            $this->orderService->handlePaymentFailure($gatewayOrderId);
        }

        $this->clearDemoSession();

        return redirect()->route('checkout.index')
                         ->withErrors(['payment' => 'Payment was cancelled. Your cart has been restored.']);
    }

    private function clearDemoSession(): void
    {
        session()->forget(['demo_gateway_order_id', 'demo_order_uuid', 'demo_order_amount']);
    }
}
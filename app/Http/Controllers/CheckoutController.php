<?php

namespace App\Http\Controllers;

use App\Http\Requests\Checkout\InitiatePaymentRequest;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\SimulatedPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private OrderService             $orderService,
        private SimulatedPaymentService  $paymentService,
    ) {
        $this->middleware('auth');
    }

    // ── 1. Show checkout page ─────────────────────────────────

    public function index()
    {
        $user      = auth()->user();
        $addresses = $user->addresses()->get();
        $cart      = app(\App\Services\CartService::class)->summary();

        if ($cart['is_empty']) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('shop.checkout.index', compact('addresses', 'cart'));
    }

    // ── 2. Initiate: create order + gateway order ─────────────

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

        // Store gateway order ID in session for the demo payment screen
        session()->put('demo_gateway_order_id', $result['gateway_order']['id']);
        session()->put('demo_order_uuid',        $result['order']->uuid);
        session()->put('demo_order_amount',       $result['gateway_order']['amount']); // paise

        return redirect()->route('checkout.demo-payment');
    }

    // ── 3. Demo payment screen ────────────────────────────────
    // Simulates the Razorpay modal — shows order summary with
    // "Pay Now" (success) and "Cancel Payment" (failure) buttons.

    public function demoPayment()
    {
        $gatewayOrderId = session('demo_gateway_order_id');
        $orderUuid      = session('demo_order_uuid');
        $amountPaise    = session('demo_order_amount');

        if (! $gatewayOrderId || ! $orderUuid) {
            return redirect()->route('checkout.index');
        }

        $order  = Order::where('uuid', $orderUuid)->firstOrFail();
        $amount = $amountPaise / 100; // convert back to rupees for display

        return view('shop.checkout.demo-payment', compact('order', 'gatewayOrderId', 'amount'));
    }

    // ── 4. Simulate success ───────────────────────────────────

    public function simulateSuccess(Request $request): RedirectResponse
    {
        $gatewayOrderId = session('demo_gateway_order_id');

        if (! $gatewayOrderId) {
            return redirect()->route('checkout.index')->withErrors(['checkout' => 'Session expired.']);
        }

        // Generate a valid signed payload — same as what Razorpay JS would POST
        $payload = $this->paymentService->simulateSuccess($gatewayOrderId);

        try {
            $order = $this->orderService->verifyAndConfirm(
                $payload['gateway_order_id'],
                $payload['gateway_payment_id'],
                $payload['gateway_signature'],
            );
        } catch (\RuntimeException $e) {
            return redirect()->route('checkout.index')->withErrors(['payment' => $e->getMessage()]);
        }

        $this->clearDemoSession();

        return redirect()->route('orders.show', $order)
                         ->with('success', '✅ Demo payment successful! Order placed.');
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
                         ->withErrors(['payment' => '❌ Demo payment failed. Stock has been restored.']);
    }

    // ── Private ───────────────────────────────────────────────

    private function clearDemoSession(): void
    {
        session()->forget(['demo_gateway_order_id', 'demo_order_uuid', 'demo_order_amount']);
    }
}
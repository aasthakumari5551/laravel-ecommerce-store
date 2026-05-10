<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use Illuminate\Support\Str;

/**
 * Drop-in replacement for RazorpayService.
 * Implements the same PaymentGateway contract.
 * Zero real transactions — safe for portfolio/demo use.
 *
 * To replace with real Razorpay:
 *   1. Create RazorpayService implementing PaymentGateway
 *   2. Change the binding in AppServiceProvider
 *   3. Done — OrderService and controllers stay untouched.
 */
class SimulatedPaymentService implements PaymentGateway
{
    private const FAKE_PUBLIC_KEY = 'DEMO_KEY_xxxxxxxxxxxx';
    private const FAKE_SECRET     = 'DEMO_SECRET';

    // ── PaymentGateway contract ───────────────────────────────

    public function createOrder(float $amount, string $receipt): array
    {
        // Mirrors Razorpay order object shape exactly
        return [
            'id'       => 'demo_order_' . Str::random(14),
            'amount'   => (int) round($amount * 100), // paise — same as Razorpay
            'currency' => 'INR',
            'receipt'  => $receipt,
            'status'   => 'created',
        ];
    }

    public function verifySignature(
        string $gatewayOrderId,
        string $gatewayPaymentId,
        string $gatewaySignature,
    ): void {
        // In demo mode: signature is an HMAC we generate ourselves in simulateSuccess()
        // Verify it the same way Razorpay would
        $expected = $this->generateSignature($gatewayOrderId, $gatewayPaymentId);

        if (! hash_equals($expected, $gatewaySignature)) {
            throw new \RuntimeException('Demo payment signature verification failed.');
        }
    }

    public function getPublicKey(): string
    {
        return self::FAKE_PUBLIC_KEY;
    }

    // ── Demo-specific helpers ─────────────────────────────────

    /**
     * Generate a fake payment payload as if the user clicked "Pay Now"
     * and the gateway returned success. Used by SimulatedCheckoutController.
     */
    public function simulateSuccess(string $gatewayOrderId): array
    {
        $paymentId = 'demo_pay_' . Str::random(14);
        $signature = $this->generateSignature($gatewayOrderId, $paymentId);

        return [
            'gateway_order_id'   => $gatewayOrderId,
            'gateway_payment_id' => $paymentId,
            'gateway_signature'  => $signature,
        ];
    }

    /**
     * Generate a fake payment payload that deliberately has a bad signature.
     * Used to demo the failure/cancellation flow.
     */
    public function simulateFailure(string $gatewayOrderId): array
    {
        return [
            'gateway_order_id'   => $gatewayOrderId,
            'gateway_payment_id' => 'demo_pay_' . Str::random(14),
            'gateway_signature'  => 'invalid_signature_' . Str::random(10),
            'error'              => 'Payment declined by demo gateway.',
        ];
    }

    // ── Private ───────────────────────────────────────────────

    /**
     * Same HMAC-SHA256 logic Razorpay uses.
     * When you plug in real Razorpay, this moves to Razorpay SDK — structure stays identical.
     */
    private function generateSignature(string $orderId, string $paymentId): string
    {
        return hash_hmac('sha256', $orderId . '|' . $paymentId, self::FAKE_SECRET);
    }
}
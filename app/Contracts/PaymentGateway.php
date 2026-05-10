<?php

namespace App\Contracts;

interface PaymentGateway
{
    /**
     * Create a payment order on the gateway side.
     * Returns a gateway order array with at minimum: id, amount, currency.
     */
    public function createOrder(float $amount, string $receipt): array;

    /**
     * Verify the payment signature/token after user completes payment.
     * Must throw \RuntimeException on failure.
     */
    public function verifySignature(
        string $gatewayOrderId,
        string $gatewayPaymentId,
        string $gatewaySignature,
    ): void;

    /**
     * Return the public key / identifier for the frontend.
     */
    public function getPublicKey(): string;
}
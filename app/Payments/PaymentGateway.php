<?php

namespace App\Payments;

use App\Models\Order;

interface PaymentGateway
{
    /**
     * Start a hosted checkout for the order and return the URL to send the customer to.
     */
    public function createCheckout(Order $order, string $successUrl, string $cancelUrl): CheckoutSession;

    /**
     * Refund the whole order.
     */
    public function refund(Order $order): void;
}

<?php

namespace App\Payments;

use App\Models\Order;
use App\Models\OrderItem;
use Stripe\StripeClient;

class StripeGateway implements PaymentGateway
{
    public function __construct(private StripeClient $stripe) {}

    public function createCheckout(Order $order, string $successUrl, string $cancelUrl): CheckoutSession
    {
        $lineItems = $order->items->map(fn (OrderItem $item) => [
            'quantity' => $item->quantity,
            'price_data' => [
                'currency' => strtolower($order->currency),
                'unit_amount' => $item->unit_price,
                'product_data' => ['name' => $item->name],
            ],
        ])->all();

        $session = $this->stripe->checkout->sessions->create(array_filter([
            'mode' => 'payment',
            'line_items' => $lineItems,
            'customer_email' => $order->email,
            'locale' => $order->locale,
            'client_reference_id' => $order->number,
            'metadata' => ['order_id' => $order->id],
            'payment_intent_data' => ['metadata' => ['order_id' => $order->id]],
            'shipping_address_collection' => ['allowed_countries' => config('shop.shipping_countries')],
            'phone_number_collection' => ['enabled' => true],
            'shipping_options' => [[
                'shipping_rate_data' => [
                    'type' => 'fixed_amount',
                    'display_name' => __('shop.shipping_name', locale: $order->locale),
                    'fixed_amount' => ['amount' => $order->shipping, 'currency' => strtolower($order->currency)],
                ],
            ]],
            'allow_promotion_codes' => true,
            'success_url' => $successUrl.'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl,
            'expires_at' => now()->addMinutes(60)->timestamp,
        ], fn ($value) => $value !== null));

        return new CheckoutSession($session->id, $session->url);
    }

    public function refund(Order $order): void
    {
        $this->stripe->refunds->create(['payment_intent' => $order->stripe_payment_intent_id]);
    }
}

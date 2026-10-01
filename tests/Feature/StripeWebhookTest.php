<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Jobs\SendOrderToSupplier;
use App\Mail\OrderConfirmed;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'whsec_test';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.stripe.webhook_secret' => self::SECRET]);
        Mail::fake();
        Queue::fake();
    }

    public function test_completed_checkout_marks_the_order_paid(): void
    {
        $variant = Product::factory()->withVariant(1990, stock: 5)->create()->variants->first();
        $order = Order::factory()->create(['stripe_session_id' => 'cs_test_42']);
        $order->items()->create(['product_variant_id' => $variant->id, 'name' => 'Case', 'sku' => $variant->sku, 'unit_price' => 1990, 'quantity' => 2]);

        $this->webhook('checkout.session.completed', $this->stripeSession('cs_test_42'))->assertOk();

        $order->refresh();
        $this->assertSame(OrderStatus::Paid, $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame('anna@example.ch', $order->email);
        $this->assertSame('pi_test_42', $order->stripe_payment_intent_id);
        $this->assertSame('Zürich', $order->shipping_address['city']);
        $this->assertSame(3970, $order->total);
        $this->assertSame(500, $order->discount);
        $this->assertSame(3, $variant->fresh()->stock);

        Mail::assertQueued(OrderConfirmed::class, fn ($mail) => $mail->hasTo('anna@example.ch') && $mail->locale === 'de');
        Queue::assertPushed(SendOrderToSupplier::class);
    }

    public function test_repeated_events_are_handled_once(): void
    {
        Order::factory()->create(['stripe_session_id' => 'cs_test_42']);

        $this->webhook('checkout.session.completed', $this->stripeSession('cs_test_42'));
        $this->webhook('checkout.session.completed', $this->stripeSession('cs_test_42'));

        Mail::assertQueuedCount(1);
        Queue::assertPushed(SendOrderToSupplier::class, 1);
    }

    public function test_unpaid_session_is_ignored(): void
    {
        $order = Order::factory()->create(['stripe_session_id' => 'cs_test_42']);

        $this->webhook('checkout.session.completed', $this->stripeSession('cs_test_42', ['payment_status' => 'unpaid']));

        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
    }

    public function test_expired_session_cancels_the_order(): void
    {
        $order = Order::factory()->create(['stripe_session_id' => 'cs_test_42']);

        $this->webhook('checkout.session.expired', $this->stripeSession('cs_test_42', ['payment_status' => 'unpaid']));

        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
    }

    public function test_refund_marks_the_order_refunded(): void
    {
        $order = Order::factory()->paid()->create();

        $this->webhook('charge.refunded', ['id' => 'ch_1', 'object' => 'charge', 'refunded' => true, 'payment_intent' => 'pi_test_123']);

        $this->assertSame(OrderStatus::Refunded, $order->fresh()->status);
    }

    public function test_invalid_signature_is_rejected(): void
    {
        $this->call('POST', '/stripe/webhook', [], [], [], ['HTTP_STRIPE_SIGNATURE' => 't=1,v1=bad', 'CONTENT_TYPE' => 'application/json'], '{}')
            ->assertStatus(400);
    }

    private function stripeSession(string $id, array $overrides = []): array
    {
        return $overrides + [
            'id' => $id,
            'object' => 'checkout.session',
            'payment_status' => 'paid',
            'payment_intent' => 'pi_test_42',
            'amount_total' => 3970,
            'total_details' => ['amount_discount' => 500],
            'customer_details' => ['email' => 'anna@example.ch', 'phone' => '+41791234567'],
            'collected_information' => ['shipping_details' => [
                'name' => 'Anna Muster',
                'address' => ['line1' => 'Bahnhofstrasse 1', 'line2' => null, 'postal_code' => '8001', 'city' => 'Zürich', 'state' => null, 'country' => 'CH'],
            ]],
        ];
    }

    private function webhook(string $type, array $object): TestResponse
    {
        $payload = json_encode(['id' => 'evt_'.uniqid(), 'object' => 'event', 'type' => $type, 'data' => ['object' => $object]]);
        $timestamp = time();
        $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", self::SECRET);

        return $this->call('POST', '/stripe/webhook', [], [], [], [
            'HTTP_STRIPE_SIGNATURE' => "t={$timestamp},v1={$signature}",
            'CONTENT_TYPE' => 'application/json',
        ], $payload);
    }
}

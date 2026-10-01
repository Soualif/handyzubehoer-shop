<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Payments\CheckoutSession;
use App\Payments\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_a_pending_order_and_redirects_to_stripe(): void
    {
        $variant = Product::factory()->withVariant(1990)->create(['name' => ['en' => 'Case', 'de' => 'Hülle']])->variants->first();

        $gateway = Mockery::mock(PaymentGateway::class);
        $gateway->shouldReceive('createCheckout')
            ->once()
            ->withArgs(fn (Order $order, string $success, string $cancel) => $order->items->count() === 1
                && str_contains($success, '/de/checkout/success/'.$order->number)
                && str_ends_with($cancel, '/de/cart'))
            ->andReturn(new CheckoutSession('cs_test_1', 'https://checkout.stripe.com/c/pay/cs_test_1'));
        $this->app->instance(PaymentGateway::class, $gateway);

        $this->post('/de/cart', ['variant' => $variant->id, 'quantity' => 2]);

        $this->post('/de/checkout')->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_1');

        $order = Order::with('items')->sole();
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertSame('de', $order->locale);
        $this->assertSame(3980, $order->subtotal);
        $this->assertSame(490, $order->shipping);
        $this->assertSame(4470, $order->total);
        $this->assertSame('cs_test_1', $order->stripe_session_id);
        $this->assertSame('Hülle', $order->items->first()->name);

        $this->get("/de/checkout/success/{$order->number}")->assertOk()->assertSee($order->number);
        $this->assertNull(session('cart'));
    }

    public function test_empty_cart_cannot_check_out(): void
    {
        $this->post('/en/checkout')->assertRedirect('/en/cart');
        $this->assertSame(0, Order::count());
    }

    public function test_success_page_is_only_shown_to_the_buyer(): void
    {
        $order = Order::factory()->create();

        $this->get("/en/checkout/success/{$order->number}")->assertNotFound();
    }
}

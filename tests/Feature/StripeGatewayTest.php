<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Payments\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;
use Tests\TestCase;

class StripeGatewayTest extends TestCase
{
    use RefreshDatabase;

    public array $requests = [];

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.stripe.secret' => 'sk_test_fake']);

        $test = $this;
        ApiRequestor::setHttpClient(new class($test) implements ClientInterface
        {
            public function __construct(private $test) {}

            public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null)
            {
                $this->test->requests[] = compact('method', 'absUrl', 'params');

                return [json_encode(['id' => 'cs_test_1', 'object' => 'checkout.session', 'url' => 'https://checkout.stripe.com/c/pay/cs_test_1']), 200, []];
            }
        });
    }

    protected function tearDown(): void
    {
        ApiRequestor::setHttpClient(null);

        parent::tearDown();
    }

    public function test_checkout_session_is_created_in_chf_in_the_customer_language(): void
    {
        $order = Order::factory()->create(['locale' => 'it', 'shipping' => 490]);
        $order->items()->create(['name' => 'Cover – Nero', 'sku' => 'A1', 'unit_price' => 1990, 'quantity' => 2]);

        $session = app(PaymentGateway::class)->createCheckout($order->load('items'), 'https://shop.test/it/checkout/success/X', 'https://shop.test/it/cart');

        $this->assertSame('cs_test_1', $session->id);
        $this->assertSame('https://checkout.stripe.com/c/pay/cs_test_1', $session->url);

        $request = $this->requests[0];
        $this->assertStringEndsWith('/v1/checkout/sessions', $request['absUrl']);
        $params = $request['params'];
        $this->assertSame('payment', $params['mode']);
        $this->assertSame('it', $params['locale']);
        $this->assertSame('chf', $params['line_items'][0]['price_data']['currency']);
        $this->assertSame(1990, $params['line_items'][0]['price_data']['unit_amount']);
        $this->assertSame(2, $params['line_items'][0]['quantity']);
        $this->assertSame(490, $params['shipping_options'][0]['shipping_rate_data']['fixed_amount']['amount']);
        $this->assertSame(['CH', 'LI'], $params['shipping_address_collection']['allowed_countries']);
        $this->assertArrayNotHasKey('customer_email', $params);
        $this->assertSame('https://shop.test/it/checkout/success/X?session_id={CHECKOUT_SESSION_ID}', $params['success_url']);
    }
}

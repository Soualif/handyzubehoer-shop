<?php

namespace Tests\Feature;

use App\Actions\ImportSupplierProduct;
use App\Enums\OrderStatus;
use App\Jobs\SendOrderToSupplier;
use App\Mail\OrderShipped;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use RefreshDatabase;

    private const BASE = 'https://developers.cjdropshipping.com/api2.0/v1/';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.cj.api_key' => 'test-key']);
    }

    public function test_import_creates_a_hidden_product_with_computed_prices(): void
    {
        Http::fake([
            self::BASE.'authentication/getAccessToken' => Http::response(['result' => true, 'data' => ['accessToken' => 'token']]),
            self::BASE.'product/query*' => Http::response(['result' => true, 'data' => [
                'pid' => 'P1',
                'productNameEn' => 'Magnetic Case for iPhone',
                'description' => '<p>Strong magnets</p><script>alert(1)</script>',
                'productImageSet' => ['https://cdn.example/1.jpg', 'https://cdn.example/2.jpg'],
                'variants' => [
                    ['vid' => 'V1', 'variantSku' => 'CJ-V1', 'variantNameEn' => 'Black', 'variantSellPrice' => 2.5, 'variantImage' => null],
                    ['vid' => 'V2', 'variantSku' => 'CJ-V2', 'variantNameEn' => 'Blue', 'variantSellPrice' => 3.1, 'variantImage' => null],
                ],
            ]]),
            self::BASE.'product/stock/queryByVid*' => Http::response(['result' => true, 'data' => [['totalInventoryNum' => 120]]]),
        ]);

        $product = app(ImportSupplierProduct::class)->handle('P1');

        $this->assertFalse($product->is_active);
        $this->assertSame('Magnetic Case for iPhone', $product->translate('name', 'de'));
        $this->assertStringNotContainsString('script', $product->translate('description'));
        $this->assertCount(2, $product->images);
        $this->assertSame([590, 690], $product->variants()->orderBy('price')->pluck('price')->all());
        $this->assertSame(120, $product->variants->first()->stock);

        Http::assertSent(fn (Request $request) => $request->hasHeader('CJ-Access-Token', 'token') || str_contains($request->url(), 'getAccessToken'));
    }

    public function test_sync_keeps_admin_edits_and_locked_prices(): void
    {
        $product = Product::factory()->create(['supplier' => 'cj', 'supplier_product_id' => 'P1', 'name' => ['en' => 'My name', 'de' => 'Mein Name'], 'is_active' => true]);
        $locked = $product->variants()->create(['sku' => 'CJ-V1', 'supplier_variant_id' => 'V1', 'price' => 2990, 'price_locked' => true, 'stock' => 1]);
        $gone = $product->variants()->create(['sku' => 'CJ-OLD', 'supplier_variant_id' => 'OLD', 'price' => 990, 'stock' => 5]);

        Http::fake([
            self::BASE.'authentication/getAccessToken' => Http::response(['result' => true, 'data' => ['accessToken' => 'token']]),
            self::BASE.'product/query*' => Http::response(['result' => true, 'data' => [
                'pid' => 'P1', 'productNameEn' => 'Supplier name', 'variants' => [
                    ['vid' => 'V1', 'variantSku' => 'CJ-V1', 'variantNameEn' => 'Black', 'variantSellPrice' => 4.0],
                ],
            ]]),
            self::BASE.'product/stock/queryByVid*' => Http::response(['result' => true, 'data' => [['totalInventoryNum' => 0]]]),
        ]);

        $this->artisan('shop:sync-products')->assertSuccessful();

        $this->assertSame('Mein Name', $product->fresh()->translate('name', 'de'));
        $this->assertTrue($product->fresh()->is_active);
        $this->assertSame(2990, $locked->fresh()->price);
        $this->assertSame(400, $locked->fresh()->cost_price);
        $this->assertSame(0, $locked->fresh()->stock);
        $this->assertFalse($gone->fresh()->is_active);
    }

    public function test_paid_order_is_sent_to_cj(): void
    {
        $variant = Product::factory()->withVariant()->create()->variants->first();
        $order = Order::factory()->paid()->create();
        $order->items()->create(['product_variant_id' => $variant->id, 'name' => 'Case', 'sku' => $variant->sku, 'unit_price' => 1990, 'quantity' => 2]);

        Http::fake([
            self::BASE.'authentication/getAccessToken' => Http::response(['result' => true, 'data' => ['accessToken' => 'token']]),
            self::BASE.'shopping/order/createOrderV2' => Http::response(['result' => true, 'data' => ['orderId' => 'CJ-ORDER-1']]),
        ]);

        SendOrderToSupplier::dispatchSync($order);

        $order->refresh();
        $this->assertSame(OrderStatus::SentToSupplier, $order->status);
        $this->assertSame('CJ-ORDER-1', $order->supplier_order_id);

        Http::assertSent(fn (Request $request) => str_ends_with($request->url(), 'createOrderV2')
            && $request['orderNumber'] === $order->number
            && $request['shippingCountryCode'] === 'CH'
            && $request['shippingCity'] === 'Zürich'
            && $request['products'] === [['vid' => $variant->supplier_variant_id, 'quantity' => 2]]);
    }

    public function test_supplier_error_is_kept_on_the_order(): void
    {
        $order = Order::factory()->paid()->create();

        Http::fake([
            self::BASE.'authentication/getAccessToken' => Http::response(['result' => true, 'data' => ['accessToken' => 'token']]),
            self::BASE.'shopping/order/createOrderV2' => Http::response(['result' => false, 'message' => 'Insufficient balance']),
        ]);

        try {
            SendOrderToSupplier::dispatchSync($order);
        } catch (\Throwable) {
        }

        $this->assertSame(OrderStatus::Paid, $order->fresh()->status);
        $this->assertStringContainsString('Insufficient balance', $order->fresh()->supplier_error);
    }

    public function test_tracking_sync_marks_shipped_and_emails_the_customer(): void
    {
        Mail::fake();
        $order = Order::factory()->paid()->create(['status' => OrderStatus::SentToSupplier, 'supplier_order_id' => 'CJ-ORDER-1']);
        $waiting = Order::factory()->paid()->create(['status' => OrderStatus::SentToSupplier, 'supplier_order_id' => 'CJ-ORDER-2']);

        Http::fake([
            self::BASE.'authentication/getAccessToken' => Http::response(['result' => true, 'data' => ['accessToken' => 'token']]),
            self::BASE.'shopping/order/getOrderDetail?orderId=CJ-ORDER-1' => Http::response(['result' => true, 'data' => ['orderStatus' => 'SHIPPED', 'trackNumber' => 'LP123CH', 'logisticName' => 'CJPacket']]),
            self::BASE.'shopping/order/getOrderDetail?orderId=CJ-ORDER-2' => Http::response(['result' => true, 'data' => ['orderStatus' => 'UNSHIPPED', 'trackNumber' => null]]),
        ]);

        $this->artisan('shop:sync-tracking')->assertSuccessful();

        $this->assertSame(OrderStatus::Shipped, $order->fresh()->status);
        $this->assertSame('LP123CH', $order->fresh()->tracking_number);
        $this->assertSame(OrderStatus::SentToSupplier, $waiting->fresh()->status);
        Mail::assertQueued(OrderShipped::class, 1);
    }
}

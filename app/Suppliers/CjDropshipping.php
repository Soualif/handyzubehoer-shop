<?php

namespace App\Suppliers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Support\Html;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * CJ Dropshipping API v2 (https://developers.cjdropshipping.com).
 */
class CjDropshipping implements Supplier
{
    public const NAME = 'cj';

    public function fetchProduct(string $productId): SupplierProduct
    {
        $data = $this->call('get', 'product/query', ['pid' => $productId]);

        $images = $data['productImageSet'] ?? json_decode($data['productImage'] ?? '[]', true) ?? [];

        return new SupplierProduct(
            id: $data['pid'],
            name: $data['productNameEn'] ?? '',
            description: Html::clean($data['description'] ?? ''),
            images: array_values(array_filter((array) $images, 'is_string')),
            variants: array_map(fn (array $variant) => [
                'id' => $variant['vid'],
                'sku' => $variant['variantSku'],
                'name' => (string) ($variant['variantNameEn'] ?? $variant['variantKey'] ?? ''),
                'cost' => (int) round(((float) $variant['variantSellPrice']) * 100),
                'image' => $variant['variantImage'] ?? null,
            ], $data['variants'] ?? []),
        );
    }

    public function stock(string $variantId): int
    {
        $warehouses = $this->call('get', 'product/stock/queryByVid', ['vid' => $variantId]);

        return (int) collect($warehouses)->sum(fn (array $warehouse) => $warehouse['totalInventoryNum'] ?? $warehouse['storageNum'] ?? 0);
    }

    public function placeOrder(Order $order): string
    {
        $address = $order->shipping_address;

        $payload = [
            'orderNumber' => $order->number,
            'shippingCountryCode' => $address['country'],
            'shippingCountry' => $address['country'] === 'LI' ? 'Liechtenstein' : 'Switzerland',
            'shippingProvince' => $address['state'] ?? '',
            'shippingCity' => $address['city'],
            'shippingAddress' => $address['line1'],
            'shippingAddress2' => $address['line2'] ?? '',
            'shippingZip' => $address['postal_code'],
            'shippingCustomerName' => $address['name'],
            'shippingPhone' => $order->phone ?? '',
            'email' => $order->email,
            'logisticName' => config('services.cj.logistic_name'),
            'fromCountryCode' => config('services.cj.from_country'),
            'products' => $order->items->map(fn (OrderItem $item) => [
                'vid' => $item->variant?->supplier_variant_id,
                'quantity' => $item->quantity,
            ])->all(),
        ];

        $data = $this->call('post', 'shopping/order/createOrderV2', $payload);

        return (string) $data['orderId'];
    }

    public function orderStatus(string $supplierOrderId): SupplierOrderStatus
    {
        $data = $this->call('get', 'shopping/order/getOrderDetail', ['orderId' => $supplierOrderId]);

        return new SupplierOrderStatus(
            status: (string) ($data['orderStatus'] ?? 'UNKNOWN'),
            trackingNumber: $data['trackNumber'] ?? null,
            carrier: $data['logisticName'] ?? null,
        );
    }

    private function call(string $method, string $path, array $data = []): mixed
    {
        $response = $this->client()->withHeaders(['CJ-Access-Token' => $this->token()])->{$method}($path, $data);

        if ($response->failed() || $response->json('result') !== true) {
            throw new SupplierException("CJ {$path}: ".($response->json('message') ?? $response->status()));
        }

        return $response->json('data');
    }

    private function token(): string
    {
        return Cache::remember('cj.access_token', now()->addDays(14), function () {
            if (blank(config('services.cj.api_key'))) {
                throw new SupplierException('CJ_API_KEY is not set.');
            }

            $response = $this->client()->post('authentication/getAccessToken', [
                'apiKey' => config('services.cj.api_key'),
            ]);

            if ($response->failed() || $response->json('result') !== true) {
                throw new SupplierException('CJ authentication failed: '.($response->json('message') ?? $response->status()));
            }

            return $response->json('data.accessToken');
        });
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl(config('services.cj.base_url'))->acceptJson()->asJson()->timeout(30)->retry(2, 1000, throw: false);
    }
}

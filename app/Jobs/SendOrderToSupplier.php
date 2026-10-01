<?php

namespace App\Jobs;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Suppliers\Supplier;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SendOrderToSupplier implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [60, 600];

    public function __construct(public Order $order) {}

    public function uniqueId(): string
    {
        return (string) $this->order->id;
    }

    public function handle(Supplier $supplier): void
    {
        $order = $this->order->fresh('items.variant');

        if ($order->status !== OrderStatus::Paid || $order->supplier_order_id) {
            return;
        }

        $order->update([
            'supplier_order_id' => $supplier->placeOrder($order),
            'status' => OrderStatus::SentToSupplier,
            'sent_to_supplier_at' => now(),
            'supplier_error' => null,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $this->order->update(['supplier_error' => $exception->getMessage()]);
    }
}

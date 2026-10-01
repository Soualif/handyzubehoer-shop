<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Mail\OrderShipped;
use App\Models\Order;
use App\Suppliers\Supplier;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

#[Signature('shop:sync-tracking')]
#[Description('Fetch tracking numbers from the supplier and tell customers their order has shipped')]
class SyncTracking extends Command
{
    public function handle(Supplier $supplier): int
    {
        Order::where('status', OrderStatus::SentToSupplier)->whereNotNull('supplier_order_id')->each(function (Order $order) use ($supplier) {
            try {
                $status = $supplier->orderStatus($order->supplier_order_id);
            } catch (Throwable $e) {
                Log::warning('Tracking sync failed', ['order' => $order->number, 'error' => $e->getMessage()]);

                return;
            }

            $order->supplier_status = $status->status;

            if ($status->isShipped()) {
                $order->fill([
                    'status' => OrderStatus::Shipped,
                    'tracking_number' => $status->trackingNumber,
                    'carrier' => $status->carrier,
                    'shipped_at' => now(),
                ]);
            }

            $order->save();

            if ($order->wasChanged('status')) {
                Mail::to($order->email)->send(new OrderShipped($order));
                $this->info("{$order->number} shipped: {$order->tracking_number}");
            }
        });

        return self::SUCCESS;
    }
}

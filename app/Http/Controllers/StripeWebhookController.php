<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Jobs\SendOrderToSupplier;
use App\Mail\OrderConfirmed;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeObject;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                (string) config('services.stripe.webhook_secret'),
            );
        } catch (SignatureVerificationException|UnexpectedValueException) {
            return response('Invalid signature', 400);
        }

        $object = $event->data->object;

        match ($event->type) {
            'checkout.session.completed', 'checkout.session.async_payment_succeeded' => $this->sessionCompleted($object),
            'checkout.session.expired', 'checkout.session.async_payment_failed' => $this->sessionFailed($object),
            'charge.refunded' => $this->refunded($object),
            default => null,
        };

        return response('OK');
    }

    private function sessionCompleted(StripeObject $session): void
    {
        // Some methods (e.g. bank transfers) complete the session before the money arrives.
        if ($session->payment_status !== 'paid') {
            return;
        }

        $order = Order::where('stripe_session_id', $session->id)->first();

        if (! $order) {
            Log::warning('Stripe session without order', ['session' => $session->id]);

            return;
        }

        $paid = DB::transaction(function () use ($order, $session) {
            $order = Order::lockForUpdate()->find($order->id);

            if ($order->status !== OrderStatus::Pending) {
                return false;
            }

            $details = $session->collected_information?->shipping_details ?? $session->shipping_details ?? null;
            $address = $details?->address;

            $order->update([
                'status' => OrderStatus::Paid,
                'paid_at' => now(),
                'email' => $session->customer_details?->email ?? $order->email,
                'phone' => $session->customer_details?->phone,
                'stripe_payment_intent_id' => $session->payment_intent,
                'discount' => $session->total_details?->amount_discount ?? 0,
                'total' => $session->amount_total,
                'shipping_address' => $address ? [
                    'name' => $details->name,
                    'line1' => $address->line1,
                    'line2' => $address->line2,
                    'postal_code' => $address->postal_code,
                    'city' => $address->city,
                    'state' => $address->state,
                    'country' => $address->country,
                ] : null,
            ]);

            foreach ($order->items as $item) {
                ProductVariant::whereKey($item->product_variant_id)
                    ->where('stock', '>=', $item->quantity)
                    ->decrement('stock', $item->quantity);
            }

            return true;
        });

        if ($paid) {
            $order = $order->fresh('items');

            Mail::to($order->email)->send(new OrderConfirmed($order));
            SendOrderToSupplier::dispatch($order);
        }
    }

    private function sessionFailed(StripeObject $session): void
    {
        Order::where('stripe_session_id', $session->id)
            ->where('status', OrderStatus::Pending)
            ->update(['status' => OrderStatus::Cancelled]);
    }

    private function refunded(StripeObject $charge): void
    {
        if (! $charge->refunded) {
            return;
        }

        Order::where('stripe_payment_intent_id', $charge->payment_intent)
            ->update(['status' => OrderStatus::Refunded]);
    }
}

<?php

namespace App\Http\Controllers\Shop;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Payments\PaymentGateway;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Turn the cart into a pending order and send the customer to Stripe Checkout.
     */
    public function store(Request $request, Cart $cart, PaymentGateway $payments): RedirectResponse
    {
        $lines = $cart->lines();

        if ($lines->isEmpty()) {
            return redirect()->route('cart.show');
        }

        $subtotal = Cart::subtotal($lines);
        $shipping = Cart::shipping($subtotal);
        $locale = app()->getLocale();

        $order = DB::transaction(function () use ($lines, $subtotal, $shipping, $locale) {
            $order = Order::create([
                'status' => OrderStatus::Pending,
                'locale' => $locale,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $subtotal + $shipping,
            ]);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_variant_id' => $line['variant']->id,
                    'name' => $line['variant']->fullName($locale),
                    'sku' => $line['variant']->sku,
                    'unit_price' => $line['variant']->price,
                    'quantity' => $line['quantity'],
                ]);
            }

            return $order;
        });

        $session = $payments->createCheckout(
            $order->load('items'),
            successUrl: route('checkout.success', $order),
            cancelUrl: route('cart.show'),
        );

        $order->update(['stripe_session_id' => $session->id]);
        $request->session()->put('last_order', $order->number);

        return redirect()->away($session->url);
    }

    /**
     * Thank-you page after Stripe. The order is marked paid by the webhook, not here.
     */
    public function success(Request $request, Order $order, Cart $cart): View
    {
        abort_unless($request->session()->get('last_order') === $order->number, 404);

        $cart->clear();

        return view('shop.checkout-success', ['order' => $order->load('items')]);
    }
}

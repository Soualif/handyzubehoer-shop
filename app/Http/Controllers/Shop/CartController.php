<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function show(Cart $cart): View
    {
        $lines = $cart->lines();
        $subtotal = Cart::subtotal($lines);

        return view('shop.cart', [
            'lines' => $lines,
            'subtotal' => $subtotal,
            'shipping' => $lines->isEmpty() ? 0 : Cart::shipping($subtotal),
        ]);
    }

    public function add(Request $request, Cart $cart): RedirectResponse
    {
        $data = $request->validate([
            'variant' => ['required', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:'.config('shop.max_quantity')],
        ]);

        $variant = ProductVariant::with('product')->findOrFail($data['variant']);

        if (! $variant->isPurchasable()) {
            return back()->withErrors(['variant' => __('shop.out_of_stock')]);
        }

        $cart->add($variant, $data['quantity'] ?? 1);

        return redirect()->route('cart.show')->with('status', __('shop.added_to_cart'));
    }

    public function update(Request $request, Cart $cart, ProductVariant $variant): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:'.config('shop.max_quantity')]]);

        $cart->set($variant, $data['quantity']);

        return redirect()->route('cart.show');
    }
}

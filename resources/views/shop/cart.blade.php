<x-layouts.shop :title="__('shop.cart')">
    <h1 class="text-2xl font-semibold">{{ __('shop.cart') }}</h1>

    @if ($lines->isEmpty())
        <p class="mt-8 text-zinc-600">{{ __('shop.cart_empty') }}</p>
        <a href="{{ route('products.index') }}" class="mt-4 inline-block text-sm font-medium underline">{{ __('shop.shop_now') }}</a>
    @else
        <ul class="mt-8 divide-y divide-zinc-200 border-y border-zinc-200">
            @foreach ($lines as $line)
                <li class="flex flex-wrap items-center gap-4 py-4">
                    <a href="{{ route('products.show', ['product' => $line['variant']->product]) }}" class="flex-1 min-w-48 text-sm font-medium hover:underline">{{ $line['variant']->fullName() }}</a>
                    <form action="{{ route('cart.update', ['variant' => $line['variant']]) }}" method="post" class="flex items-center gap-2">
                        @csrf
                        @method('patch')
                        <label class="sr-only" for="qty-{{ $line['variant']->id }}">{{ __('shop.quantity') }}</label>
                        <input id="qty-{{ $line['variant']->id }}" type="number" name="quantity" value="{{ $line['quantity'] }}" min="0" max="{{ config('shop.max_quantity') }}" class="w-16 rounded-md border border-zinc-300 px-2 py-1 text-sm">
                        <button class="text-sm underline">{{ __('shop.update') }}</button>
                        <button name="quantity" value="0" class="text-sm text-zinc-500 underline">{{ __('shop.remove') }}</button>
                    </form>
                    <span class="w-28 text-right text-sm">{{ \App\Support\Money::format($line['total']) }}</span>
                </li>
            @endforeach
        </ul>

        <dl class="ml-auto mt-6 max-w-xs space-y-2 text-sm">
            <div class="flex justify-between"><dt>{{ __('shop.subtotal') }}</dt><dd>{{ \App\Support\Money::format($subtotal) }}</dd></div>
            <div class="flex justify-between"><dt>{{ __('shop.shipping') }}</dt><dd>{{ $shipping === 0 ? __('shop.free') : \App\Support\Money::format($shipping) }}</dd></div>
            <div class="flex justify-between border-t border-zinc-200 pt-2 text-base font-semibold"><dt>{{ __('shop.total') }}</dt><dd>{{ \App\Support\Money::format($subtotal + $shipping) }}</dd></div>
            <p class="text-xs text-zinc-500">{{ __('shop.vat_included') }}</p>
        </dl>

        <form action="{{ route('checkout') }}" method="post" class="ml-auto mt-6 max-w-xs">
            @csrf
            <button class="w-full rounded-md bg-zinc-900 px-5 py-3 text-sm font-semibold text-white hover:bg-zinc-700">{{ __('shop.checkout') }}</button>
            <p class="mt-2 text-xs text-zinc-500">{{ __('shop.checkout_hint') }}</p>
            <p class="mt-2 text-xs text-zinc-500">{!! __('shop.accept_terms', ['link' => '<a class="underline" href="'.e(route('page', ['page' => 'terms'])).'">'.e(__('shop.pages.terms')).'</a>']) !!}</p>
        </form>
    @endif
</x-layouts.shop>

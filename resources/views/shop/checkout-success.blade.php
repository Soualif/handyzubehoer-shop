<x-layouts.shop :title="__('shop.thank_you')">
    <h1 class="text-2xl font-semibold">{{ __('shop.thank_you') }}</h1>
    <p class="mt-4 text-zinc-700">{{ __('shop.order_received', ['number' => $order->number]) }}</p>
    <p class="mt-2 text-zinc-700">{{ __('shop.confirmation_email') }}</p>

    <ul class="mt-8 max-w-lg divide-y divide-zinc-200 border-y border-zinc-200 text-sm">
        @foreach ($order->items as $item)
            <li class="flex justify-between gap-4 py-3"><span>{{ $item->quantity }} × {{ $item->name }}</span><span>{{ \App\Support\Money::format($item->lineTotal()) }}</span></li>
        @endforeach
    </ul>

    <a href="{{ route('home') }}" class="mt-8 inline-block text-sm font-medium underline">{{ __('shop.continue_shopping') }}</a>
</x-layouts.shop>

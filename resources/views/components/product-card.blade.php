@props(['product'])

<a href="{{ route('products.show', ['product' => $product]) }}" class="group block">
    <div class="aspect-square overflow-hidden rounded-lg bg-zinc-100">
        @if ($image = $product->mainImageUrl())
            <img src="{{ $image }}" alt="{{ $product->translate('name') }}" loading="lazy"
                class="h-full w-full object-cover transition group-hover:scale-105">
        @endif
    </div>
    <h3 class="mt-3 text-sm font-medium leading-snug group-hover:underline">{{ $product->translate('name') }}</h3>
    @if (($price = $product->lowestPrice()) !== null)
        <p class="mt-1 text-sm text-zinc-600">
            @if ($product->variants->where('is_active', true)->pluck('price')->unique()->count() > 1)
                {{ __('shop.from') }}
            @endif
            {{ \App\Support\Money::format($price) }}
        </p>
    @endif
</a>

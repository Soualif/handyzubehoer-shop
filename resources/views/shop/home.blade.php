<x-layouts.shop :description="__('shop.tagline')">
    <section class="rounded-2xl bg-zinc-900 px-6 py-14 text-white sm:px-12">
        <h1 class="max-w-2xl text-3xl font-semibold tracking-tight sm:text-4xl">{{ __('shop.hero_title') }}</h1>
        <p class="mt-4 max-w-xl text-zinc-300">{{ __('shop.tagline') }}</p>
        <a href="{{ route('products.index') }}" class="mt-8 inline-block rounded-md bg-white px-5 py-2.5 text-sm font-semibold text-zinc-900 hover:bg-zinc-200">{{ __('shop.shop_now') }}</a>
    </section>

    <ul class="mt-8 grid gap-4 text-sm text-zinc-600 sm:grid-cols-3">
        <li class="rounded-lg border border-zinc-200 px-4 py-3">{{ __('shop.usp_shipping', ['amount' => \App\Support\Money::format(config('shop.free_shipping_from'))]) }}</li>
        <li class="rounded-lg border border-zinc-200 px-4 py-3">{{ __('shop.usp_payment') }}</li>
        <li class="rounded-lg border border-zinc-200 px-4 py-3">{{ __('shop.usp_vat') }}</li>
    </ul>

    @if ($products->isNotEmpty())
        <h2 class="mt-12 text-xl font-semibold">{{ __('shop.new_arrivals') }}</h2>
        <div class="mt-6 grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    @else
        <p class="mt-12 text-zinc-600">{{ __('shop.opening_soon') }}</p>
    @endif
</x-layouts.shop>

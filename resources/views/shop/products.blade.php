@php
    $heading = $category?->translate('name') ?? ($search !== '' ? __('shop.results_for', ['q' => $search]) : __('shop.all_products'));
@endphp

<x-layouts.shop :title="$heading">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h1 class="text-2xl font-semibold">{{ $heading }}</h1>

        <form method="get" class="flex items-center gap-2 text-sm">
            @if ($search !== '')
                <input type="hidden" name="q" value="{{ $search }}">
            @endif
            <label for="device" class="text-zinc-600">{{ __('shop.my_phone') }}</label>
            <select id="device" name="device" onchange="this.form.submit()" class="rounded-md border border-zinc-300 px-2 py-1.5">
                <option value="">{{ __('shop.all_models') }}</option>
                @foreach ($devices as $brand => $models)
                    <optgroup label="{{ $brand }}">
                        @foreach ($models as $model)
                            <option value="{{ $model->slug }}" @selected($device?->is($model))>{{ $model->name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            <noscript><button class="rounded-md border px-2 py-1.5">OK</button></noscript>
        </form>
    </div>

    @if ($products->isEmpty())
        <p class="mt-10 text-zinc-600">{{ __('shop.no_products') }}</p>
    @else
        <div class="mt-8 grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
        <div class="mt-10">{{ $products->links() }}</div>
    @endif
</x-layouts.shop>

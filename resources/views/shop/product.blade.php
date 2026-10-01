<x-layouts.shop :title="$product->translate('name')" :description="\Illuminate\Support\Str::limit(strip_tags($product->translate('description')), 155)">
    <div class="grid gap-10 md:grid-cols-2">
        <div class="space-y-3">
            @forelse ($product->images as $image)
                <img src="{{ $image->url }}" alt="{{ $product->translate('name') }}" @class(['w-full rounded-lg bg-zinc-100', 'hidden sm:block' => ! $loop->first]) @if (! $loop->first) loading="lazy" @endif>
            @empty
                <div class="aspect-square rounded-lg bg-zinc-100"></div>
            @endforelse
        </div>

        <div>
            @if ($product->category)
                <a href="{{ route('categories.show', ['category' => $product->category]) }}" class="text-sm text-zinc-500 hover:underline">{{ $product->category->translate('name') }}</a>
            @endif
            <h1 class="mt-1 text-2xl font-semibold">{{ $product->translate('name') }}</h1>

            <form action="{{ route('cart.add') }}" method="post" class="mt-6 space-y-4">
                @csrf
                @if ($product->variants->count() > 1)
                    <fieldset>
                        <legend class="text-sm font-medium">{{ __('shop.choose_variant') }}</legend>
                        <div class="mt-2 space-y-2">
                            @foreach ($product->variants as $variant)
                                <label @class(['flex items-center justify-between gap-3 rounded-md border px-3 py-2 text-sm', 'opacity-50' => $variant->stock < 1])>
                                    <span class="flex items-center gap-2">
                                        <input type="radio" name="variant" value="{{ $variant->id }}" @checked($loop->first && $variant->stock > 0) @disabled($variant->stock < 1) required>
                                        {{ $variant->translate('name') ?: $variant->sku }}
                                    </span>
                                    <span>{{ $variant->stock < 1 ? __('shop.out_of_stock') : \App\Support\Money::format($variant->price) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @else
                    @php($variant = $product->variants->first())
                    <input type="hidden" name="variant" value="{{ $variant->id }}">
                    <p class="text-xl">
                        {{ \App\Support\Money::format($variant->price) }}
                        @if ($variant->compare_at_price > $variant->price)
                            <s class="ml-2 text-base text-zinc-500">{{ \App\Support\Money::format($variant->compare_at_price) }}</s>
                        @endif
                    </p>
                @endif
                <p class="text-xs text-zinc-500">{{ __('shop.vat_included') }}</p>

                <div class="flex items-center gap-3">
                    <label for="quantity" class="text-sm">{{ __('shop.quantity') }}</label>
                    <input id="quantity" type="number" name="quantity" value="1" min="1" max="{{ config('shop.max_quantity') }}" class="w-20 rounded-md border border-zinc-300 px-2 py-1.5">
                </div>

                @if ($product->variants->where('stock', '>', 0)->isNotEmpty())
                    <button class="w-full rounded-md bg-zinc-900 px-5 py-3 text-sm font-semibold text-white hover:bg-zinc-700">{{ __('shop.add_to_cart') }}</button>
                @else
                    <p class="font-medium text-zinc-600">{{ __('shop.out_of_stock') }}</p>
                @endif
            </form>

            <p class="mt-4 text-sm text-zinc-600">{{ __('shop.delivery_estimate') }}</p>

            @if ($product->deviceModels->isNotEmpty())
                <h2 class="mt-8 text-sm font-semibold">{{ __('shop.compatible_with') }}</h2>
                <p class="mt-1 text-sm text-zinc-600">{{ $product->deviceModels->map->fullName()->join(', ') }}</p>
            @endif

            <div class="mt-8 space-y-3 text-sm leading-relaxed text-zinc-700 [&_ul]:list-disc [&_ul]:pl-5">{!! \App\Support\Html::clean($product->translate('description')) !!}</div>
        </div>
    </div>
</x-layouts.shop>

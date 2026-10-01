<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    @isset($description)
        <meta name="description" content="{{ $description }}">
    @endisset
    @foreach (config('app.supported_locales') as $locale)
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ \App\Support\Locale::switchUrl($locale) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white text-zinc-800 antialiased flex flex-col">
    <header class="border-b border-zinc-200">
        <div class="mx-auto max-w-6xl px-4 py-4 flex flex-wrap items-center gap-4">
            <a href="{{ route('home') }}" class="text-lg font-semibold tracking-tight">{{ config('app.name') }}</a>

            <form action="{{ route('products.index') }}" method="get" class="order-last w-full sm:order-none sm:w-auto sm:flex-1">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('shop.search_placeholder') }}"
                    class="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-500 focus:outline-none">
            </form>

            <nav class="ml-auto flex items-center gap-4 text-sm">
                <a href="{{ route('products.index') }}" class="hover:underline">{{ __('shop.all_products') }}</a>
                <a href="{{ route('cart.show') }}" class="font-medium hover:underline">
                    {{ __('shop.cart') }} ({{ app(\App\Support\Cart::class)->count() }})
                </a>
            </nav>
        </div>
        @if ($categories->isNotEmpty())
            <nav class="mx-auto max-w-6xl px-4 pb-3 flex gap-4 overflow-x-auto text-sm text-zinc-600">
                @foreach ($categories as $navCategory)
                    <a href="{{ route('categories.show', ['category' => $navCategory]) }}" class="whitespace-nowrap hover:text-zinc-900">{{ $navCategory->translate('name') }}</a>
                @endforeach
            </nav>
        @endif
    </header>

    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8">
        @if (session('status'))
            <p class="mb-6 rounded-md bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</p>
        @endif
        @if ($errors->any())
            <p class="mb-6 rounded-md bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</p>
        @endif

        {{ $slot }}
    </main>

    <footer class="border-t border-zinc-200 bg-zinc-50 text-sm text-zinc-600">
        <div class="mx-auto max-w-6xl px-4 py-8 flex flex-wrap gap-x-6 gap-y-3">
            <a href="{{ route('page', ['page' => 'impressum']) }}" class="hover:underline">{{ __('shop.pages.impressum') }}</a>
            <a href="{{ route('page', ['page' => 'terms']) }}" class="hover:underline">{{ __('shop.pages.terms') }}</a>
            <a href="{{ route('page', ['page' => 'privacy']) }}" class="hover:underline">{{ __('shop.pages.privacy') }}</a>
            <a href="{{ route('page', ['page' => 'shipping-returns']) }}" class="hover:underline">{{ __('shop.pages.shipping-returns') }}</a>

            <nav aria-label="{{ __('shop.language') }}" class="ml-auto flex gap-3">
                @foreach (config('app.supported_locales') as $locale)
                    <a href="{{ \App\Support\Locale::switchUrl($locale) }}" hreflang="{{ $locale }}"
                        @class(['font-semibold text-zinc-900' => $locale === app()->getLocale(), 'hover:underline'])
                        @if ($locale === app()->getLocale()) aria-current="true" @endif>{{ strtoupper($locale) }}</a>
                @endforeach
            </nav>
        </div>
    </footer>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @foreach (config('app.supported_locales') as $locale)
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ route('home', ['locale' => $locale]) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; padding: 2rem 1rem; max-width: 48rem; margin-inline: auto; color: #1f2937; }
        nav a { margin-right: .75rem; color: #6b7280; text-decoration: none; }
        nav a[aria-current] { color: #111827; font-weight: 600; }
    </style>
</head>
<body>
    <nav aria-label="{{ __('Language') }}">
        @foreach (config('app.supported_locales') as $locale)
            <a href="{{ route('home', ['locale' => $locale]) }}" hreflang="{{ $locale }}" @if ($locale === app()->getLocale()) aria-current="true" @endif>{{ strtoupper($locale) }}</a>
        @endforeach
    </nav>

    <h1>{{ __('Mobile accessories delivered across Switzerland') }}</h1>
    <p>{{ __('Cases, chargers, cables and more for your smartphone.') }}</p>
    <p>{{ __('Shop opening soon.') }}</p>
</body>
</html>

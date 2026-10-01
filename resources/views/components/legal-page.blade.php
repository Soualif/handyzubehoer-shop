@props(['title'])

<x-layouts.shop :title="$title">
    <article class="max-w-3xl space-y-4 text-sm leading-relaxed text-zinc-700 [&_h2]:mt-8 [&_h2]:text-base [&_h2]:font-semibold [&_h2]:text-zinc-900 [&_ul]:list-disc [&_ul]:pl-5">
        <h1 class="text-2xl font-semibold text-zinc-900">{{ $title }}</h1>
        {{ $slot }}
    </article>
</x-layouts.shop>

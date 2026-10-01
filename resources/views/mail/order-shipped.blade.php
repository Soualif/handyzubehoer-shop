<x-mail::message>
# {{ __('mail.shipped_title') }}

{{ __('mail.shipped_intro', ['number' => $order->number]) }}

**{{ __('mail.tracking_number') }}:** {{ $order->tracking_number }}
@if ($order->carrier)
<br>**{{ __('mail.carrier') }}:** {{ $order->carrier }}
@endif

<x-mail::button :url="'https://parcelsapp.com/'.app()->getLocale().'/tracking/'.urlencode($order->tracking_number)">
{{ __('mail.track_parcel') }}
</x-mail::button>

{{ __('mail.shipped_delay') }}

{{ __('mail.signature') }}<br>
{{ config('app.name') }}
</x-mail::message>

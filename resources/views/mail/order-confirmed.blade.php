<x-mail::message>
# {{ __('mail.confirmed_title') }}

{{ __('mail.confirmed_intro', ['number' => $order->number]) }}

<x-mail::table>
| {{ __('mail.item') }} | {{ __('mail.qty') }} | {{ __('mail.price') }} |
|:--|:-:|--:|
@foreach ($order->items as $item)
| {{ $item->name }} | {{ $item->quantity }} | {{ \App\Support\Money::format($item->lineTotal()) }} |
@endforeach
| {{ __('shop.shipping') }} | | {{ $order->shipping === 0 ? __('shop.free') : \App\Support\Money::format($order->shipping) }} |
@if ($order->discount > 0)
| {{ __('mail.discount') }} | | −{{ \App\Support\Money::format($order->discount) }} |
@endif
| **{{ __('shop.total') }}** | | **{{ \App\Support\Money::format($order->total) }}** |
</x-mail::table>

@if ($order->shipping_address)
**{{ __('mail.shipping_to') }}**<br>
{{ $order->shipping_address['name'] }}<br>
{{ $order->shipping_address['line1'] }}<br>
@if (! empty($order->shipping_address['line2']))
{{ $order->shipping_address['line2'] }}<br>
@endif
{{ $order->shipping_address['postal_code'] }} {{ $order->shipping_address['city'] }}
@endif

{{ __('mail.confirmed_next') }}

{{ __('mail.signature') }}<br>
{{ config('app.name') }}
</x-mail::message>

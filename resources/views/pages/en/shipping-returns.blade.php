<x-legal-page title="Shipping and returns">
<h2>Shipping</h2>
<ul>
<li>Delivery to Switzerland and Liechtenstein.</li>
<li>Shipping: {{ \App\Support\Money::format(config('shop.shipping_fee')) }}, free from {{ \App\Support\Money::format(config('shop.free_shipping_from')) }}.</li>
<li>Delivery time: usually 7 to 15 working days. You receive a tracking number by e-mail.</li>
</ul>
<h2>Returns</h2>
<p>Not right for you? You can return unused items in their original packaging within 14 days of receipt. Please e-mail us first to get the return address. Return shipping is at your expense. We refund the price to the original payment method once we receive the item.</p>
<h2>Defective items</h2>
<p>Send us a photo of the defect within 14 days. You get a free replacement or a refund without having to send the item back.</p>
</x-legal-page>

<x-legal-page title="Legal notice">
<h2>Responsible for this website</h2>
<p>
    {{ config('shop.company.name') }}<br>
    @if (config('shop.company.owner')){{ config('shop.company.owner') }}<br>@endif
    {{ config('shop.company.address') }}<br>
    E-mail: {{ config('shop.company.email') }}<br>
    @if (config('shop.company.phone'))Phone: {{ config('shop.company.phone') }}<br>@endif
    @if (config('shop.company.uid'))UID / VAT no.: {{ config('shop.company.uid') }}@endif
</p>
<h2>Disclaimer</h2>
<p>We check the content of this website carefully but cannot guarantee that it is accurate, complete or up to date. The operators of linked websites are solely responsible for their content.</p>
</x-legal-page>

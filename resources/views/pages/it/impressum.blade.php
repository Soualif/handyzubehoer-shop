<x-legal-page title="Note legali">
<h2>Responsabile di questo sito</h2>
<p>
    {{ config('shop.company.name') }}<br>
    @if (config('shop.company.owner')){{ config('shop.company.owner') }}<br>@endif
    {{ config('shop.company.address') }}<br>
    E-mail: {{ config('shop.company.email') }}<br>
    @if (config('shop.company.phone'))Telefono: {{ config('shop.company.phone') }}<br>@endif
    @if (config('shop.company.uid'))IDI / N. IVA: {{ config('shop.company.uid') }}@endif
</p>
<h2>Esclusione di responsabilità</h2>
<p>Verifichiamo con cura i contenuti di questo sito, senza garantirne l'esattezza, la completezza e l'attualità. I gestori dei siti collegati sono gli unici responsabili dei loro contenuti.</p>
</x-legal-page>

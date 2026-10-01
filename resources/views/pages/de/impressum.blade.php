<x-legal-page title="Impressum">
<h2>Verantwortlich für diese Website</h2>
<p>
    {{ config('shop.company.name') }}<br>
    @if (config('shop.company.owner')){{ config('shop.company.owner') }}<br>@endif
    {{ config('shop.company.address') }}<br>
    E-Mail: {{ config('shop.company.email') }}<br>
    @if (config('shop.company.phone'))Telefon: {{ config('shop.company.phone') }}<br>@endif
    @if (config('shop.company.uid'))UID / MWST-Nr.: {{ config('shop.company.uid') }}@endif
</p>
<h2>Haftungsausschluss</h2>
<p>Wir prüfen die Inhalte dieser Website sorgfältig, übernehmen aber keine Gewähr für Richtigkeit, Vollständigkeit und Aktualität. Für Inhalte verlinkter Websites sind ausschliesslich deren Betreiber verantwortlich.</p>
</x-legal-page>

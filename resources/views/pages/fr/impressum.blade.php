<x-legal-page title="Mentions légales">
<h2>Responsable de ce site</h2>
<p>
    {{ config('shop.company.name') }}<br>
    @if (config('shop.company.owner')){{ config('shop.company.owner') }}<br>@endif
    {{ config('shop.company.address') }}<br>
    E-mail: {{ config('shop.company.email') }}<br>
    @if (config('shop.company.phone'))Téléphone: {{ config('shop.company.phone') }}<br>@endif
    @if (config('shop.company.uid'))IDE / N° TVA: {{ config('shop.company.uid') }}@endif
</p>
<h2>Exclusion de responsabilité</h2>
<p>Nous vérifions soigneusement le contenu de ce site, sans garantir son exactitude, son exhaustivité ni son actualité. Les exploitants des sites liés sont seuls responsables de leur contenu.</p>
</x-legal-page>

<x-legal-page title="Politique de protection des données">
<p>Nous traitons les données personnelles conformément à la loi fédérale sur la protection des données (LPD).</p>
<h2>Responsable du traitement</h2>
<p>
    {{ config('shop.company.name') }}<br>
    @if (config('shop.company.owner')){{ config('shop.company.owner') }}<br>@endif
    {{ config('shop.company.address') }}<br>
    E-mail: {{ config('shop.company.email') }}<br>
    @if (config('shop.company.phone'))Téléphone: {{ config('shop.company.phone') }}<br>@endif
    @if (config('shop.company.uid'))IDE / N° TVA: {{ config('shop.company.uid') }}@endif
</p>
<h2>Données traitées</h2>
<ul>
<li>Données de commande : nom, adresse de livraison, e-mail, téléphone, produits commandés.</li>
<li>Données de paiement : traitées directement par Stripe. Nous ne recevons pas les numéros de carte complets.</li>
<li>Données techniques : journaux du serveur et un cookie pour votre session, votre panier et votre choix de langue.</li>
</ul>
<h2>Finalité</h2>
<p>Ces données servent uniquement à traiter votre commande, à vous informer de l'expédition et à respecter nos obligations légales (comptabilité, par exemple).</p>
<h2>Transmission à des tiers et à l'étranger</h2>
<ul>
<li>Stripe Payments Europe Ltd. (Irlande) pour le paiement.</li>
<li>Notre fournisseur CJ Dropshipping (Chine) reçoit votre nom, votre adresse de livraison et votre téléphone pour expédier le colis. La Chine n'offre pas un niveau de protection reconnu comme adéquat par le Conseil fédéral ; la transmission est nécessaire à l'exécution du contrat (art. 17 al. 1 let. b LPD).</li>
<li>Les transporteurs, pour la livraison.</li>
</ul>
<h2>Conservation</h2>
<p>Nous conservons les données de commande pendant le délai légal de 10 ans.</p>
<h2>Vos droits</h2>
<p>Vous pouvez demander l'accès, la rectification ou l'effacement de vos données en nous écrivant à l'adresse e-mail ci-dessus.</p>
</x-legal-page>

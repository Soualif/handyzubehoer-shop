<x-legal-page title="Livraison et retours">
<h2>Livraison</h2>
<ul>
<li>Livraison en Suisse et au Liechtenstein.</li>
<li>Frais de livraison : {{ \App\Support\Money::format(config('shop.shipping_fee')) }}, offerts dès {{ \App\Support\Money::format(config('shop.free_shipping_from')) }}.</li>
<li>Délai : généralement 7 à 15 jours ouvrables. Vous recevez un numéro de suivi par e-mail.</li>
</ul>
<h2>Retours</h2>
<p>L'article ne convient pas ? Vous pouvez retourner les articles non utilisés, dans leur emballage d'origine, dans les 14 jours suivant la réception. Contactez-nous d'abord par e-mail pour obtenir l'adresse de retour. Les frais de retour sont à votre charge. Nous remboursons le prix sur le moyen de paiement d'origine dès réception.</p>
<h2>Articles défectueux</h2>
<p>Envoyez-nous une photo du défaut dans les 14 jours. Vous recevez gratuitement un remplacement ou un remboursement, sans avoir à renvoyer l'article.</p>
</x-legal-page>

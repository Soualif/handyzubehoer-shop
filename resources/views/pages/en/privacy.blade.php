<x-legal-page title="Privacy policy">
<p>We process personal data in accordance with the Swiss Federal Act on Data Protection (FADP).</p>
<h2>Controller</h2>
<p>
    {{ config('shop.company.name') }}<br>
    @if (config('shop.company.owner')){{ config('shop.company.owner') }}<br>@endif
    {{ config('shop.company.address') }}<br>
    E-mail: {{ config('shop.company.email') }}<br>
    @if (config('shop.company.phone'))Phone: {{ config('shop.company.phone') }}<br>@endif
    @if (config('shop.company.uid'))UID / VAT no.: {{ config('shop.company.uid') }}@endif
</p>
<h2>Data we process</h2>
<ul>
<li>Order data: name, delivery address, e-mail, phone number, products ordered.</li>
<li>Payment data: processed directly by Stripe. We never receive full card numbers.</li>
<li>Technical data: server logs and a cookie for your session, cart and language choice.</li>
</ul>
<h2>Purpose</h2>
<p>We use this data only to fulfil your order, tell you about shipping and meet legal obligations (such as bookkeeping).</p>
<h2>Disclosure to third parties and abroad</h2>
<ul>
<li>Stripe Payments Europe Ltd. (Ireland) for payment processing.</li>
<li>Our supplier CJ Dropshipping (China) receives your name, delivery address and phone number to ship the parcel. China does not have a level of data protection recognised as adequate by the Federal Council; the transfer is necessary to perform the contract (Art. 17(1)(b) FADP).</li>
<li>Carriers, for delivery.</li>
</ul>
<h2>Retention</h2>
<p>We keep order data for the statutory period of 10 years.</p>
<h2>Your rights</h2>
<p>You can ask to access, correct or delete your data by writing to the e-mail address above.</p>
</x-legal-page>

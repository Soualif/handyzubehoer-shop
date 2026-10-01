<x-legal-page title="Datenschutzerklärung">
<p>Wir bearbeiten Personendaten gemäss dem Schweizer Datenschutzgesetz (DSG).</p>
<h2>Verantwortliche Stelle</h2>
<p>
    {{ config('shop.company.name') }}<br>
    @if (config('shop.company.owner')){{ config('shop.company.owner') }}<br>@endif
    {{ config('shop.company.address') }}<br>
    E-Mail: {{ config('shop.company.email') }}<br>
    @if (config('shop.company.phone'))Telefon: {{ config('shop.company.phone') }}<br>@endif
    @if (config('shop.company.uid'))UID / MWST-Nr.: {{ config('shop.company.uid') }}@endif
</p>
<h2>Welche Daten wir bearbeiten</h2>
<ul>
<li>Bestelldaten: Name, Lieferadresse, E-Mail, Telefonnummer, bestellte Produkte.</li>
<li>Zahlungsdaten: werden direkt von Stripe bearbeitet. Wir erhalten keine vollständigen Kartendaten.</li>
<li>Technische Daten: Server-Logs und ein Cookie für Ihre Sitzung, Ihren Warenkorb und Ihre Sprachwahl.</li>
</ul>
<h2>Zweck</h2>
<p>Wir verwenden diese Daten ausschliesslich, um Ihre Bestellung abzuwickeln, Sie über den Versand zu informieren und gesetzliche Pflichten (z. B. Buchhaltung) zu erfüllen.</p>
<h2>Weitergabe an Dritte und ins Ausland</h2>
<ul>
<li>Stripe Payments Europe Ltd. (Irland) für die Zahlungsabwicklung.</li>
<li>Unser Lieferant CJ Dropshipping (China) erhält Name, Lieferadresse und Telefonnummer, um das Paket zu versenden. China verfügt nicht über ein vom Bundesrat als angemessen anerkanntes Datenschutzniveau; die Übermittlung ist für die Vertragserfüllung erforderlich (Art. 17 Abs. 1 lit. b DSG).</li>
<li>Versanddienstleister für die Zustellung.</li>
</ul>
<h2>Aufbewahrung</h2>
<p>Bestelldaten bewahren wir während der gesetzlichen Aufbewahrungsfrist von 10 Jahren auf.</p>
<h2>Ihre Rechte</h2>
<p>Sie können Auskunft, Berichtigung oder Löschung Ihrer Daten verlangen. Schreiben Sie uns an die oben genannte E-Mail-Adresse.</p>
</x-legal-page>

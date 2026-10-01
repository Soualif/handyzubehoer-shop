<x-legal-page title="Versand und Rückgabe">
<h2>Versand</h2>
<ul>
<li>Lieferung in die Schweiz und nach Liechtenstein.</li>
<li>Versandkosten: {{ \App\Support\Money::format(config('shop.shipping_fee')) }}, gratis ab {{ \App\Support\Money::format(config('shop.free_shipping_from')) }}.</li>
<li>Lieferzeit: in der Regel 7 bis 15 Werktage. Sie erhalten eine Sendungsnummer per E-Mail.</li>
</ul>
<h2>Rückgabe</h2>
<p>Nicht passend? Sie können ungebrauchte Artikel in der Originalverpackung innert 14 Tagen nach Erhalt zurückgeben. Kontaktieren Sie uns vorher per E-Mail, wir teilen Ihnen die Rücksendeadresse mit. Die Kosten der Rücksendung tragen Sie. Nach Eingang erstatten wir den Kaufpreis über das ursprüngliche Zahlungsmittel.</p>
<h2>Defekte Artikel</h2>
<p>Senden Sie uns innert 14 Tagen ein Foto des Mangels. Sie erhalten kostenlos Ersatz oder eine Rückerstattung, ohne den Artikel zurücksenden zu müssen.</p>
</x-legal-page>

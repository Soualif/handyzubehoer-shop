<x-legal-page title="Spedizione e resi">
<h2>Spedizione</h2>
<ul>
<li>Consegna in Svizzera e nel Liechtenstein.</li>
<li>Spese di spedizione: {{ \App\Support\Money::format(config('shop.shipping_fee')) }}, gratuite da {{ \App\Support\Money::format(config('shop.free_shipping_from')) }}.</li>
<li>Termine: di norma 7–15 giorni lavorativi. Ricevi un numero di tracciamento via e-mail.</li>
</ul>
<h2>Resi</h2>
<p>Non va bene? Puoi restituire gli articoli non usati, nell'imballaggio originale, entro 14 giorni dal ricevimento. Contattaci prima via e-mail per ricevere l'indirizzo di reso. Le spese di reso sono a tuo carico. Rimborsiamo il prezzo sul mezzo di pagamento originale al ricevimento.</p>
<h2>Articoli difettosi</h2>
<p>Inviaci una foto del difetto entro 14 giorni. Ricevi gratuitamente una sostituzione o un rimborso, senza dover restituire l'articolo.</p>
</x-legal-page>

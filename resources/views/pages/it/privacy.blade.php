<x-legal-page title="Informativa sulla protezione dei dati">
<p>Trattiamo i dati personali secondo la legge federale sulla protezione dei dati (LPD).</p>
<h2>Titolare del trattamento</h2>
<p>
    {{ config('shop.company.name') }}<br>
    @if (config('shop.company.owner')){{ config('shop.company.owner') }}<br>@endif
    {{ config('shop.company.address') }}<br>
    E-mail: {{ config('shop.company.email') }}<br>
    @if (config('shop.company.phone'))Telefono: {{ config('shop.company.phone') }}<br>@endif
    @if (config('shop.company.uid'))IDI / N. IVA: {{ config('shop.company.uid') }}@endif
</p>
<h2>Dati trattati</h2>
<ul>
<li>Dati dell'ordine: nome, indirizzo di consegna, e-mail, telefono, prodotti ordinati.</li>
<li>Dati di pagamento: trattati direttamente da Stripe. Non riceviamo i numeri completi delle carte.</li>
<li>Dati tecnici: log del server e un cookie per la sessione, il carrello e la scelta della lingua.</li>
</ul>
<h2>Finalità</h2>
<p>Usiamo questi dati solo per evadere l'ordine, informarti sulla spedizione e adempiere agli obblighi legali (ad esempio la contabilità).</p>
<h2>Comunicazione a terzi e all'estero</h2>
<ul>
<li>Stripe Payments Europe Ltd. (Irlanda) per il pagamento.</li>
<li>Il nostro fornitore CJ Dropshipping (Cina) riceve nome, indirizzo di consegna e telefono per spedire il pacco. La Cina non offre un livello di protezione riconosciuto come adeguato dal Consiglio federale; la comunicazione è necessaria per l'esecuzione del contratto (art. 17 cpv. 1 lett. b LPD).</li>
<li>I corrieri, per la consegna.</li>
</ul>
<h2>Conservazione</h2>
<p>Conserviamo i dati degli ordini per il periodo legale di 10 anni.</p>
<h2>I tuoi diritti</h2>
<p>Puoi chiedere l'accesso, la rettifica o la cancellazione dei tuoi dati scrivendo all'indirizzo e-mail indicato sopra.</p>
</x-legal-page>

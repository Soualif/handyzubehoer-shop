<?php

return [

    /*
    | Shipping fee in CHF cents, and the order subtotal from which shipping is free.
    */

    'shipping_fee' => (int) env('SHOP_SHIPPING_FEE', 490),

    'free_shipping_from' => (int) env('SHOP_FREE_SHIPPING_FROM', 4900),

    /*
    | Countries customers can ship to (Stripe Checkout address form).
    */

    'shipping_countries' => ['CH', 'LI'],

    /*
    | Pricing of imported supplier products: supplier price (USD) converted to CHF,
    | multiplied by the markup, plus Swiss VAT, rounded up to the next .90.
    */

    'usd_to_chf' => (float) env('SHOP_USD_TO_CHF', 0.80),

    'markup' => (float) env('SHOP_MARKUP', 2.5),

    'vat_rate' => 0.081,

    /*
    | Maximum quantity of one variant in the cart.
    */

    'max_quantity' => 10,

    /*
    | Shop identity, shown in the Impressum and the e-mails. Fill these in .env.
    */

    'company' => [
        'name' => env('SHOP_COMPANY_NAME') ?: 'Handyzubehör Shop',
        'owner' => env('SHOP_COMPANY_OWNER'),
        'address' => env('SHOP_COMPANY_ADDRESS'),
        'email' => env('SHOP_COMPANY_EMAIL') ?: env('MAIL_FROM_ADDRESS'),
        'phone' => env('SHOP_COMPANY_PHONE'),
        'uid' => env('SHOP_COMPANY_UID'),
    ],

];

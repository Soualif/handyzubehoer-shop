<?php

namespace App\Support;

class Pricing
{
    /**
     * Sale price in CHF cents (VAT included) for a supplier cost in USD cents,
     * rounded up to end in .90 (e.g. 14.90, 19.90).
     */
    public static function salePrice(int $costUsdCents): int
    {
        $chf = $costUsdCents * config('shop.usd_to_chf') * config('shop.markup') * (1 + config('shop.vat_rate'));

        return max(490, (int) (ceil(($chf + 10) / 100) * 100) - 10);
    }
}

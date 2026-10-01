<?php

namespace App\Support;

class Money
{
    /**
     * Swiss formatting of an amount in cents: 1234590 → "CHF 12'345.90".
     */
    public static function format(int $cents, string $currency = 'CHF'): string
    {
        return $currency.' '.number_format($cents / 100, 2, '.', "'");
    }
}

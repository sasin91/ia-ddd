<?php

namespace App\Domains\Billing\Contracts;

use Illuminate\Support\Fluent;

/**
 * @method static Fluent make(string $from, string $to)
 * @property-read string $from
 * @property-read string $to
 * @property-read array $pair
 * @property-read float $value
 */
interface ExchangeRatePair
{
    //
}

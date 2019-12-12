<?php

namespace App\Domains\Billing\ExchangeRate;

use App\Domains\Billing\Contracts\ExchangeRatePair;
use App\Domains\Billing\ExchangeRate;
use Swap\Laravel\Facades\Swap;

class LatestPair implements ExchangeRatePair
{
    /**
     * @var string
     */
    public $from;

    /**
     * @var string
     */
    public $to;

    /**
     * @var array
     */
    public $pair;

    /**
     * @var float
     */
    public $value;

    public static function make(string $from, string $to): ExchangeRatePair
    {
        return tap(new static, function ($instance) use ($from, $to) {
            $pair = ExchangeRate::makePair($to, $from);

            $instance->from = $from;
            $instance->to = $to;
            $instance->pair = $pair;
            $instance->value = Swap::latest($pair)->getValue();
        });
    }
}

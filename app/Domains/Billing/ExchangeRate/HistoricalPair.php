<?php

namespace App\Domains\Billing\ExchangeRate;

use DateTimeInterface;
use Swap\Laravel\Facades\Swap;
use Illuminate\Support\Facades\Date;
use App\Domains\Billing\ExchangeRate;
use App\Domains\Billing\Contracts\ExchangeRatePair;

class HistoricalPair implements ExchangeRatePair
{
    /**
     * @var DateTimeInterface
     */
    public $date;

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

    public static function make(string $from, string $to, $date): ExchangeRatePair
    {
        return tap(new static, function ($instance) use ($from, $to, $date) {
            $pair = ExchangeRate::makePair($to, $from);

            $instance->date = Date::parse($date);
            $instance->from = $from;
            $instance->to = $to;
            $instance->pair = $pair;
            $instance->value = Swap::historical($pair, $instance->date)->getValue();
        });
    }
}

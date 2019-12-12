<?php

namespace App\Domains\Billing;

use function head;
use function config;
use function is_array;
use Illuminate\Support\Facades\Date;
use App\Domains\Billing\ExchangeRate\LatestPair;
use App\Domains\Billing\Contracts\ExchangeRatePair;
use App\Domains\Billing\ExchangeRate\HistoricalPair;
use App\Domains\Billing\Collections\ExchangeRateCollection;

class ExchangeRate
{
    public static function find(string $to, string $from = null, $date = null): ExchangeRatePair
    {
        if (!$date || Date::parse($date)->isToday()) {
            return LatestPair::make($from, $to);
        }

        return HistoricalPair::make($from, $to, $date);
    }

    public static function all($date = null): ExchangeRateCollection
    {
        return ExchangeRateCollection::make(static::currencyPairs())->map(function (string $pair) use ($date) {
            [$from, $to] = explode('/', $pair, 2);

            return static::find($to, $from, $date);
        });
    }

    public static function currencyPairs() : array
    {
        return config('currency.pairs');
    }

    public static function makePair(string $to = null, string $from = null): ?string
    {
        if (!$to) {
            return null;
        }

        if (!$from) {
            $from = config('currency.default');
        }

        return "{$from}/{$to}";
    }

    public static function fake(array $values): void
    {
        config(['swap.services' => [
            'array' => is_array(head($values)) ? $values : [$values]
        ]]);
    }
}

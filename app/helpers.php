<?php

use App\Domains\Billing\ExchangeRate;

if (! function_exists('points_for')) {
    /**
     * Calculate the points equal to given amount.
     *
     * @param  int $amount
     * @param string|null $currencyCode
     * @param DateTimeInterface|string|null $atDate
     * @return int
     */
    function points_for(int $amount, string $currencyCode = null, $atDate = null)
    {
        // When given a currencyCode that's not the default, we'll convert the amount into the default currency.
        if ($currencyCode && $currencyCode !== config('currency.default')) {
            $exchangeRate = exchange_rate($currencyCode, config('currency.default'), $atDate);

            $amount *= $exchangeRate;
        }

        return (int)($amount / 100) * 0.8;
    }
}

if (!function_exists('is_email')) {
    /**
     * Determines whether an email was given.
     *
     * @param  mixed $email
     * @return boolean
     */
    function is_email($email)
    {
        return is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}

if (!function_exists('exchange_rate')) {
    function exchange_rate(string $fromCurrency, string $toCurrency, $atDate = null)
    {
        return ExchangeRate::find($toCurrency, $fromCurrency, $atDate);
    }
}

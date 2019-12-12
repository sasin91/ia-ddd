<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domains\Billing\BillingMethod;
use App\Domains\Billing\Enums\RevenueCategory;
use App\Domains\Billing\ExchangeRate\LatestPair;
use App\Domains\Billing\Models\Revenue;
use Faker\Generator as Faker;

$factory->define(Revenue::class, function (Faker $faker) {
    $currency = $faker->randomElement(config('currency.supported'));

    return [
        'account_id' => null,
        'customer_email' => $faker->safeEmail,
        'amount' => $faker->numberBetween(0, 5000),
        'earned_points' => 0,
        'exchange_rate' => LatestPair::make(config('currency.default'), $currency)->value,
        'currency_code' => $currency,
        'description' => $faker->bs,
        'category' => $faker->randomElement(RevenueCategory::getValues()),
        'billing_method' => $faker->randomElement(BillingMethod::all()),
        'reference' => '',
        'refunded_at' => null,
        'paid_at' => null
    ];
});

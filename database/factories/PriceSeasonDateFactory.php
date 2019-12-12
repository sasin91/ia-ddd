<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domains\Booking\Models\PriceSeason;
use App\Domains\Booking\Models\PriceSeasonDate;
use Faker\Generator as Faker;

$factory->define(PriceSeasonDate::class, function (Faker $faker) {
    return [
        'season_id' => factory(PriceSeason::class),
        'starts_at' => $startsAt = $faker->dateTime,
        'ends_at' => (clone $startsAt)->modify('+'.rand(1, 5).'m')
    ];
});

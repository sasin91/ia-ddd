<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domains\Booking\Enums\TripType;
use App\Domains\Booking\Models\AgeGroup;
use App\Domains\Booking\Models\PriceSeason;
use App\Domains\Booking\Models\Price;
use App\Domains\Booking\Models\Travel;
use Faker\Generator as Faker;

$factory->define(Price::class, function (Faker $faker) {
    return [
        'travel_id' => factory(Travel::class),
        'price_season_id' => factory(PriceSeason::class),
        'type' => $faker->randomElement(TripType::getValues()),
        'age_group' => function () {
            return factory(AgeGroup::class)->create()->name;
        },
        'currency' => $faker->randomElement(config('currency.supported')),
        'amount' => $faker->numberBetween(2000, 5400)
    ];
});

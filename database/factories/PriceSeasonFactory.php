<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domains\Booking\Models\PriceSeason;
use Faker\Generator as Faker;

$factory->define(PriceSeason::class, function (Faker $faker) {
    return [
        'name' => $faker->randomElement(['high', 'low'])
    ];
});

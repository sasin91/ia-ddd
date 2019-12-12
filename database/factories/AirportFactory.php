<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domains\Booking\Enums\Country;
use App\Domains\Booking\Models\Airport;
use Faker\Generator as Faker;

$factory->define(Airport::class, function (Faker $faker) {
    return [
        'timezone' => $faker->timezone,
        'location' => $faker->address,
        'country' => $faker->randomElement(Country::getValues()),
        'IATA' => $faker->countryISOAlpha3,
    ];
});

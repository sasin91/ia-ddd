<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domains\Booking\Enums\TravelClass;
use App\Domains\Booking\Models\Airport;
use App\Domains\Booking\Models\Travel;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Travel::class, function (Faker $faker) {
    return [
        'flight_number' => Str::random(5),
        'travel_class' => $faker->randomElement(TravelClass::getValues()),
        'departure_airport' => function () {
            return factory(Airport::class)->create()->IATA;
        },
        'destination_airport' => function () {
            return factory(Airport::class)->create()->IATA;
        },
        'default_seats' => 135,
        'open_until' => now()->addYears(rand(1, 2))
    ];
});

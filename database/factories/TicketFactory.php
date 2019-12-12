<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domains\Booking\Models\Trip;
use App\Domains\Booking\Enums\TravelClass;
use App\Domains\Booking\Enums\TripType;
use App\Domains\Booking\Models\Ticket;
use App\Domains\Booking\Models\Passenger;
use App\Domains\Booking\Models\PriceSeason;
use App\Domains\Booking\Models\Price;
use App\Domains\Booking\Models\Travel;
use Faker\Generator as Faker;

$factory->define(Trip::class, function (Faker $faker) {
    return [
        'outward_travel' => function () {
            return factory(Travel::class)->create()->flight_number;
        },
        'outward_departure_datetime' => $departureDT = $faker->dateTime,
        'outward_arrival_datetime' => (clone $departureDT)->modify('+'.$faker->numberBetween(1, 5).'h'),

        'type' => $faker->randomElement(
            TripType::getValues()
        ),
        'travel_class' => $faker->randomElement(TravelClass::getValues()),

        'PNR' => function () {
            return factory(Ticket::class)->create()->PNR;
        },
        'passenger_id' => factory(Passenger::class)
    ];
});

$factory->afterMaking(Trip::class, function (Trip $ticket, Faker $faker) {
    if ($ticket->price_id === null) {
        $ticket->price()->associate(
            factory(Price::class)->create([
                'type' => $ticket->type,
                'age_group' => $ticket->passenger->age_group
            ])
        );
    }

    if ($ticket->type->isNot(TripType::ONEWAY)) {
        if (!$ticket->home_travel) {
            $ticket->home_travel = factory(Travel::class)->create()->flight_number;
        }

        if (!$ticket->home_departure_datetime) {
            $ticket->home_departure_datetime = $faker->dateTimeBetween($ticket->outward_departure_datetime, now());
        }

        if (!$ticket->home_arrival_datetime) {
            $ticket->home_arrival_datetime = (clone $ticket->home_departure_datetime)->modify('+'.$faker->numberBetween(1, 5).'h');
        }
    }
});

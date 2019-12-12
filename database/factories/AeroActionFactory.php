<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domains\Aero\Enums\AeroActionType;
use App\Domains\Aero\Models\Aero;
use App\Domains\Aero\Models\AeroAction;
use App\Domains\Booking\Models\Trip;
use Faker\Generator as Faker;

$factory->define(AeroAction::class, function (Faker $faker) {
    return [
        'ticket_id' => factory(Trip::class),
        'aero_id' => factory(Aero::class),
        'command' => 'FAKE COMMAND',
        'type' => $faker->randomElement(AeroActionType::getValues()),
        'e_ticket' => '007'.$faker->randomNumber(7),
        'tax' => $faker->randomNumber(2),
        'price' => $faker->numberBetween(0, 3780),
        'executed_at' => null
    ];
});

<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domains\Booking\Models\Ticket;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Ticket::class, function (Faker $faker) {
    return [
        'PNR' => Str::upper(Str::random(5)),
        'buyer_email' => $faker->safeEmail,
        'buyer_id' => null,
        'express' => $faker->boolean(30),
        'total_cost' => $faker->numberBetween(2000, 12000),
        'voided_at' => null,
        'documents_sent_at' => null
    ];
});

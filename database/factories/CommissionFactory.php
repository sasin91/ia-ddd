<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domains\Agent\Models\Account;
use App\Domains\Agent\Models\Commission;
use App\Domains\Booking\Models\Travel;
use Faker\Generator as Faker;

$factory->define(Commission::class, function (Faker $faker) {
    return [
        'account_id' => factory(Account::class),
        'travel_id' => factory(Travel::class),
        'base' => 75,
        'extra' => 0,
        'points_percentage' => 0.8
    ];
});

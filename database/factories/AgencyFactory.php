<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domains\Agent\Models\Agency;
use App\User;
use Faker\Generator as Faker;

$factory->define(Agency::class, function (Faker $faker) {
    return [
        'owner_id' => factory(User::class),
        'company' => $faker->company,
        'name' => $faker->bs,
        'phone' => $faker->phoneNumber,
        'address' => $faker->streetAddress,
        'country' => $faker->country
    ];
});

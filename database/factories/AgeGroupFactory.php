<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domains\Booking\Models\AgeGroup;
use Faker\Generator as Faker;

$factory->define(AgeGroup::class, function (Faker $faker) {
    return [
        'name' => $name = $faker->randomElement(['Infant', 'Child', 'Adult']),

        'icon' => [
            'Infant' => 'fas fa-baby',
            'Child' => 'fas fa-child',
            'Adult' => 'fas fa-user'
        ][$name],
        'from' => [
            'Infant' => 0,
            'Child' => 3,
            'Adult' => 12
        ][$name],
        'to' => [
            'Infant' => 3,
            'Child' => 12,
            'Adult' => 120
        ][$name],
        'passport_required' => in_array($name, ['Adult', 'Child']),
        'luggage_limit' => [
            'Infant' => 0,
            'Child' => 5,
            'Adult' => 10
        ][$name]
    ];
});

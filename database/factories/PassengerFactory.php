<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Domains\Booking\Models\Passenger;
use App\Domains\Booking\Enums\Citizenship;
use App\Domains\Booking\Enums\Nationality;
use App\Domains\Booking\Enums\PassengerGender;
use App\Domains\Booking\Enums\PassengerTitle;
use App\Domains\Booking\Models\AgeGroup;
use Faker\Generator as Faker;

$factory->define(Passenger::class, function (Faker $faker) {
    $gender = $faker->randomElement(PassengerGender::getValues());

    return [
        'creator_id' => null,
        'age_group' => function () {
            return factory(AgeGroup::class)->create()->name;
        },
        'title' => $faker->randomElement(PassengerTitle::getValues()),
        'gender' => $gender,
        'name' => $faker->name($gender),
        'phone' => $faker->phoneNumber,
        'birthdate' => $faker->date,
        'nationality' => $faker->randomElement(Nationality::getValues()),
        'citizenship' => $faker->randomElement(Citizenship::getValues()),
        'passport' => $faker->bankAccountNumber,
        'passport_expires_at' => now()->addMonths(rand(6, 12)),
        'visa' => null,
        'visa_country' => null,
        'visa_expires_at' => null
    ];
});

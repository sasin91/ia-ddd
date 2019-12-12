<?php

namespace Tests\Feature;

use App\Domains\Billing\BillingMethod;
use App\Domains\Billing\ExchangeRate;
use App\Domains\Booking\Enums\Citizenship;
use App\Domains\Booking\Enums\Nationality;
use App\Domains\Booking\Enums\PassengerGender;
use App\Domains\Booking\Enums\PassengerTitle;
use App\Domains\Booking\Enums\TravelClass;
use App\Domains\Booking\Enums\TripType;
use App\Domains\Booking\Events\BookTickets;
use App\Domains\Booking\Models\AgeGroup;
use App\Domains\Booking\Models\Ticket;
use App\Domains\Booking\Models\Passenger;
use App\Domains\Booking\Models\Trip;
use App\Domains\Booking\Models\Price;
use App\Domains\Booking\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;
use function factory;

class BookTicketsTest extends TestCase
{
    use RefreshDatabase;

    public function testTheEventProjectorGetsInvokedAndStoresTheModels()
    {
        ExchangeRate::fake(['DKK/DKK' => 1]);

        Date::setTestNow('2019-11-07 09:30:00');

        $ageGroup = factory(AgeGroup::class)->create();

        $outwardTravel = factory(Travel::class)->create(['flight_number' => 'IA282']);
        $homeTravel = factory(Travel::class)->create(['flight_number' => 'IA281']);

        // Technically price.amount is redundant in this case; it just signals that it's the withdrawn amount.
        $ticketPrice = factory(Price::class)->create(['amount' => 1000]);
        $revenue = BillingMethod::make('bank')->withdraw(1000, 'john@example.com', ['currency' => 'DKK']);

        event(new BookTickets(
            [
                'PNR' => 'ABCDE1',
                'buyer_email' => 'john@example.com'
            ],
            [
                [
                    'passenger' => [
                        'age_group' => $ageGroup->name,
                        'title' => PassengerTitle::MR,
                        'name' => 'John Doe',
                        'gender' => PassengerGender::MALE,
                        'phone' => '+45 70707070',
                        'birthdate' => '1985-07-11',
                        'nationality' => Nationality::DK,
                        'citizenship' => Citizenship::DK,
                        'passport' => '0132465789',
                        'passport_expires_at' => '2022-12-24'
                    ],

                    'type' => TripType::ONE_MONTH,
                    'travel_class' => TravelClass::ECONOMY,

                    'outward_travel' => $outwardTravel->flight_number,
                    'outward_departure_datetime' => '2019-12-01 10:00:00',
                    'outward_arrival_datetime' => '2019-12-01 13:00:00',

                    'home_travel' => $homeTravel->flight_number,
                    'home_departure_datetime' => '2019-12-29 08:00:00',
                    'home_arrival_datetime' => '2019-12-29 12:00:00',

                    'price_id' => $ticketPrice->id
                ]
            ],
            $revenue->id
        ));

        $this->assertDatabaseHas('stored_events', [
            'event_class' => BookTickets::class,
            'created_at' => '2019-11-07 09:30:00'
        ]);

        $this->assertDatabaseHas('tickets', [
            'PNR' => 'ABCDE1',
            'buyer_email' => 'john@example.com',
            'created_at' => '2019-11-07 09:30:00'
        ]);

        $this->assertDatabaseHas('tickets', [
            'PNR' => 'ABCDE1',
            'type' => TripType::ONE_MONTH,
            'travel_class' => TravelClass::ECONOMY,
            'outward_travel' => $outwardTravel->flight_number,
            'outward_departure_datetime' => '2019-12-01 10:00:00',
            'outward_arrival_datetime' => '2019-12-01 13:00:00',
            'home_travel' => $homeTravel->flight_number,
            'home_departure_datetime' => '2019-12-29 08:00:00',
            'home_arrival_datetime' => '2019-12-29 12:00:00'
        ]);

        $this->assertDatabaseHas('passengers', [
            'age_group' => $ageGroup->name,
            'title' => PassengerTitle::MR,
            'name' => 'John Doe',
            'gender' => PassengerGender::MALE,
            'phone' => '+45 70707070',
            'birthdate' => '1985-07-11',
            'nationality' => Nationality::DK,
            'citizenship' => Citizenship::DK,
            //'passport' => '0132465789',
            'passport_expires_at' => '2022-12-24'
        ]);
    }
}

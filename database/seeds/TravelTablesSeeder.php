<?php

use App\Domains\Booking\Enums\TravelClass;
use App\Domains\Booking\Models\Airport;
use App\Domains\Booking\Models\Travel;
use Illuminate\Database\Seeder;

class TravelTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $airports = Airport::all(['IATA', 'id']);
        $CPH = $airports->firstWhere('IATA', 'CPH')->IATA;
        $BGW = $airports->firstWhere('IATA', 'BGW')->IATA;
        $ISU = $airports->firstWhere('IATA', 'ISU')->IATA;
        $NJF = $airports->firstWhere('IATA', 'NJF')->IATA;
        $EBL = $airports->firstWhere('IATA', 'EBL')->IATA;

        /** @var Travel $IA282 */
        $IA282 = Travel::query()->create([
            'flight_number' => 'IA282',
            'travel_class' => TravelClass::ECONOMY,
            'departure_airport' => $CPH,
            'destination_airport' => $BGW,
            'default_seats' => 135,
            'open_until' => '2019-12-31 00:00:00'
        ]);

        $IA282->stopovers()->createMany([
            [
                'airport_IATA' => $ISU,
                'weekday' => 'Monday',
                'arrival_time' => '21:00:00',
                'departure_time' => '22:00:00'
            ],
            [
                'airport_IATA' => $NJF,
                'weekday' => 'Tuesday',
                'arrival_time' => '21:00:00',
                'departure_time' => '21:30:00'
            ],
            [
                'airport_IATA' => $EBL,
                'weekday' => 'Friday',
                'arrival_time' => '21:00:00',
                'departure_time' => '22:00:00'
            ]
        ]);

        $IA282->times()->createMany([
            [
                'weekday' => 'Monday',
                'departure_time' => '15:00:00',
                'arrival_time' => '23:00:00'
            ],
            [
                'weekday' => 'Tuesday',
                'departure_time' => '15:00:00',
                'arrival_time' => '22:00:00'
            ],
            [
                'weekday' => 'Friday',
                'departure_time' => '15:00:00',
                'arrival_time' => '23:00:00'
            ],
            [
                'weekday' => 'Sunday',
                'departure_time' => '14:30:00',
                'arrival_time' => '20:30:00'
            ],
        ]);

        /** @var Travel $IA281 */
        $IA281 = Travel::query()->create([
            'flight_number' => 'IA281',
            'travel_class' => TravelClass::ECONOMY,
            'departure_airport' => $BGW,
            'destination_airport' => $CPH,
            'default_seats' => 135,
            'open_until' => '2019-12-31 00:00:00'
        ]);

        $IA281->stopovers()->createMany([
            [
                'airport_IATA' => $ISU,
                'weekday' => 'Monday',
                'arrival_time' => '09:00:00',
                'departure_time' => '10:00:00'
            ],
            [
                'airport_IATA' => $NJF,
                'weekday' => 'Tuesday',
                'arrival_time' => '09:00:00',
                'departure_time' => '09:30:00'
            ],
            [
                'airport_IATA' => $EBL,
                'weekday' => 'Friday',
                'arrival_time' => '09:00:00',
                'departure_time' => '09:30:00'
            ]
        ]);

        $IA281->times()->createMany([
            [
                'weekday' => 'Monday',
                'departure_time' => '08:00:00',
                'arrival_time' => '14:00:00'
            ],
            [
                'weekday' => 'Tuesday',
                'departure_time' => '08:00:00',
                'arrival_time' => '14:00:00'
            ],
            [
                'weekday' => 'Friday',
                'departure_time' => '08:00:00',
                'arrival_time' => '14:00:00'
            ],
            [
                'weekday' => 'Sunday',
                'departure_time' => '09:00:00',
                'arrival_time' => '13:30:00'
            ]
        ]);
    }
}

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
        $CPH = $airports->firstWhere('IATA', 'CPH')->id;
        $BGW = $airports->firstWhere('IATA', 'BGW')->id;
        $ISU = $airports->firstWhere('IATA', 'ISU')->id;
        $NJF = $airports->firstWhere('IATA', 'NJF')->id;
        $EBL = $airports->firstWhere('IATA', 'EBL')->id;

        /** @var Travel $IA282 */
        $IA282 = Travel::query()->create([
            'flight_number' => 'IA282',
            'travel_class' => TravelClass::ECONOMY,
            'departure_airport_id' => $CPH,
            'destination_airport_id' => $BGW,
            'default_seats' => 135,
            'open_until' => '2019-12-31 00:00:00'
        ]);

        $IA282->stopovers()->createMany([
            [
                'airport_id' => $ISU,
                'weekday' => 'Monday',
                'arrival_time' => '21:00:00',
                'departure_time' => '22:00:00'
            ],
            [
                'airport_id' => $NJF,
                'weekday' => 'Tuesday',
                'arrival_time' => '21:00:00',
                'departure_time' => '21:30:00'
            ],
            [
                'airport_id' => $EBL,
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
            'departure_airport_id' => $BGW,
            'destination_airport_id' => $CPH,
            'default_seats' => 135,
            'open_until' => '2019-12-31 00:00:00'
        ]);

        $IA281->stopovers()->createMany([
            [
                'airport_id' => $ISU,
                'weekday' => 'Monday',
                'arrival_time' => '09:00:00',
                'departure_time' => '10:00:00'
            ],
            [
                'airport_id' => $NJF,
                'weekday' => 'Tuesday',
                'arrival_time' => '09:00:00',
                'departure_time' => '09:30:00'
            ],
            [
                'airport_id' => $EBL,
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

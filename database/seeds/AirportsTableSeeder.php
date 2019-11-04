<?php

use App\Domains\Booking\Models\Airport;
use Illuminate\Database\Seeder;

class AirportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Airport::query()->insert([
            [
                'timezone' => 'Europe/Copenhagen',
                'location' => 'Copenhagen Airport, Kastrup',
                'country' => 'Denmark',
                'IATA' => 'CPH',
            ],
            [
                'timezone' => 'Asia/Baghdad',
                'location' => 'Erbil International Airport',
                'country' => 'Iraq',
                'IATA' => 'EBL',
            ],
            [
                'timezone' => 'Asia/Baghdad',
                'location' => 'Baghdad International Airport',
                'country' => 'Iraq',
                'IATA' => 'BGW',
            ],
            [
                'timezone' => 'Asia/Baghdad',
                'location' => 'Al-Najaf International Airport',
                'country' => 'Iraq',
                'IATA' => 'NJF',
            ],
            [
                'timezone' => 'Asia/Baghdad',
                'location' => 'Sulaymaniyah International Airport',
                'country' => 'Iraq',
                'IATA' => 'ISU'
            ]
        ]);
    }
}

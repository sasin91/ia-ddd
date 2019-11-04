<?php

use App\Domains\Booking\Models\PriceSeason;
use Illuminate\Database\Seeder;

class PriceSeasonTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var PriceSeason $high */
        /** @var PriceSeason $low */
        $high = PriceSeason::query()->create(['name' => 'high']);
        $low = PriceSeason::query()->create(['name' => 'low']);

        $low->dates()->createMany([
            [
                'starts_at' => '2020-01-11',
                'ends_at' => '2020-03-31'
            ],
            [
                'starts_at' => '2020-04-11',
                'ends_at' => '2020-06-14'
            ],
            [
                'starts_at' => '2020-10-22',
                'ends_at' => '2020-12-09'
            ]
        ]);

        $high->dates()->createMany([
            [
               'starts_at' => '2020-04-01',
               'ends_at' => '2020-04-10',
            ],
            [
                'starts_at' => '2020-06-15',
                'ends_at' => '2020-10-21'
            ],
            [
                'starts_at' => '2020-12-10',
                'ends_at' => '2021-01-10'
            ]
        ]);
    }
}

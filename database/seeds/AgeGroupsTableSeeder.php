<?php

use App\Domains\Booking\Models\AgeGroup;
use Illuminate\Database\Seeder;

class AgeGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AgeGroup::query()->insert([
            [
                'name' => 'Infant',
                'icon' => 'fas fa-baby',
                'from' => 0,
                'to' => 3,
                'passport_required' => false,
                'luggage_limit' => 0
            ],
            [
                'name' => 'Child',
                'icon' => 'fas fa-child',
                'from' => 3,
                'to' => 12,
                'passport_required' => true,
                'luggage_limit' => 5
            ],
            [
                'name' => 'Adult',
                'icon' => 'fas fa-user',
                'from' => 12,
                'to' => 120,
                'passport_required' => true,
                'luggage_limit' => 10
            ]
        ]);
    }
}

<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionTablesSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(AgeGroupsTableSeeder::class);

        $this->call(AirportsTableSeeder::class);
        $this->call(TravelTablesSeeder::class);
        $this->call(PriceSeasonTablesSeeder::class);
        $this->call(AeroTablesSeeder::class);

        //$this->call(FakeBookingsSeeder::class);
    }
}

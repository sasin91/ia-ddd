<?php

use Illuminate\Database\Seeder;
use App\Domains\Aero\Models\Aero;

class AeroTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Aero::query()->create([
            'name' => 'SITA',
            'terminal_ip' => '10.0.0.3',
            'terminal_emulator' => 'UTS'
        ]);
    }
}

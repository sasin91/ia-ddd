<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = Hash::make('password');

        foreach (Role::all() as $role) {
            factory(User::class)
                ->create([
                    'email' => Str::lower($role->name) . '@iraqiairways.info',
                    'name' => $role->name,
                    'username' => $role->name,
                    'password' => $password,
                    'email_verified_at' => now()
                ])
                ->assignRole($role);
        }
    }
}

<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionTablesSeeder extends Seeder
{
    public function run(PermissionRegistrar $permissionRegistrar)
    {
        $permissionRegistrar->forgetCachedPermissions();

        $agent = Role::query()->create([
            'name' => 'Agent',
            'guard_name' => 'web'
        ]);

        $receptionist = Role::query()->create([
            'name' => 'Receptionist',
            'guard_name' => 'web'
        ]);

        $accountant = Role::query()->create([
            'name' => 'Accountant',
            'guard_name' => 'web'
        ]);

        $owner = Role::query()->create([
            'name' => 'Owner',
            'guard_name' => 'web'
        ]);

        $developer = Role::query()->create([
            'name' => 'Developer',
            'guard_name' => 'web'
        ]);

        Permission::query()->create([
            'name' => 'view Nova',
            'guard_name' => 'web'
        ])->roles()->saveMany([$receptionist, $accountant, $owner, $developer]);
    }
}

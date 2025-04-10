<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Berechtigungen anlegen
        Permission::create(['name' => 'edit posts']);
        Permission::create(['name' => 'delete posts']);

        // Rollen anlegen
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $user = Role::create(['name' => 'user']);
        $user->givePermissionTo(['edit posts']);
    }
}

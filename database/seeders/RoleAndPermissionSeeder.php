<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        // app()[\Spatie\\Permission\\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions if needed (example)
        // Permission::create(['name' => 'manage outlets']);
        // Permission::create(['name' => 'manage products']);
        // Permission::create(['name' => 'manage categories']);
        // Permission::create(['name' => 'manage tables']);
        // Permission::create(['name' => 'manage promotions']);
        // Permission::create(['name' => 'view dashboard']);
        // Permission::create(['name' => 'process orders']);

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $cashierRole = Role::firstOrCreate(['name' => 'kasir']);
        Role::firstOrCreate(['name' => 'cashier']);
        $userRole = Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'costumer']);
        Role::firstOrCreate(['name' => 'customer']);

        // Example: Assign all existing permissions to admin
        // $adminRole->givePermissionTo(Permission::all());

        // Example: Assign specific permissions to cashier
        // $cashierRole->givePermissionTo(['process orders'])
    }
}

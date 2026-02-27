<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan peran sudah ada
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $kasirRole = Role::firstOrCreate(['name' => 'kasir']);
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);
        $costumerRole = Role::firstOrCreate(['name' => 'costumer']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Buat user admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Admin Solusi Kopi',
                'password' => Hash::make('password'),
                'phone' => '081234567890',
            ]
        );
        $admin->syncRoles([$adminRole->name]);

        // Buat user kasir (legacy role: kasir)
        $kasir = User::updateOrCreate(
            ['email' => 'kasir@mail.com'],
            [
                'name' => 'Kasir Solusi Kopi',
                'password' => Hash::make('password'),
                'phone' => '089876543210',
            ]
        );
        $kasir->syncRoles([$kasirRole->name]);

        // Buat user cashier (english role)
        $cashier = User::updateOrCreate(
            ['email' => 'cashier@mail.com'],
            [
                'name' => 'Cashier Solusi Kopi',
                'password' => Hash::make('password'),
                'phone' => '089876543211',
            ]
        );
        $cashier->syncRoles([$cashierRole->name]);

        // Buat user biasa/pelanggan
        $regularUser = User::updateOrCreate(
            ['email' => 'user@mail.com'],
            [
                'name' => 'Pelanggan Biasa',
                'password' => Hash::make('password'),
                'phone' => '085000000000',
            ]
        );
        $regularUser->syncRoles([$userRole->name]);

        // Buat user costumer
        $costumer = User::updateOrCreate(
            ['email' => 'costumer@mail.com'],
            [
                'name' => 'Costumer Solusi Kopi',
                'password' => Hash::make('password'),
                'phone' => '085000000001',
            ]
        );
        $costumer->syncRoles([$costumerRole->name]);

        // Tambahkan user lain jika diperlukan
        User::factory(10)->create()->each(function ($user) use ($userRole) {
            $user->assignRole($userRole);
        });
    }
}

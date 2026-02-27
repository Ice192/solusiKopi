<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Outlet;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Outlet::updateOrCreate(
            ['email' => 'outlet@solusikopi.com'],
            [
                'name' => 'Solusi Kopi Pusat',
                'address' => 'Jl. Solusi Kopi No. 1, Jakarta',
                'phone' => '0215550101',
                'logo' => null,
                'opening_hours' => 'Mon-Sun: 08:00-22:00',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
            ]
        );
    }
}

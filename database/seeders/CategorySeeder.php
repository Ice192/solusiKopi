<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Outlet;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $outlet = Outlet::first();
        if (!$outlet) {
            return;
        }

        $categories = [
            ['name' => 'Kopi', 'description' => 'Aneka kopi panas dan dingin'],
            ['name' => 'Non Kopi', 'description' => 'Minuman non kopi'],
            ['name' => 'Tea', 'description' => 'Aneka teh dan turunannya'],
            ['name' => 'Snack', 'description' => 'Camilan ringan'],
            ['name' => 'Main Course', 'description' => 'Makanan utama'],
        ];

        foreach ($categories as $item) {
            Category::updateOrCreate(
                ['name' => $item['name']],
                [
                    'outlet_id' => $outlet->id,
                    'description' => $item['description'],
                    'image' => null,
                    'status' => 'active',
                ]
            );
        }
    }
}

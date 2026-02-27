<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Outlet;
use App\Models\Category;

class ProductSeeder extends Seeder
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

        $categories = Category::where('outlet_id', $outlet->id)->get()->keyBy('name');
        if ($categories->isEmpty()) {
            return;
        }

        $products = [
            ['name' => 'Espresso', 'category' => 'Kopi', 'price' => 18000],
            ['name' => 'Americano', 'category' => 'Kopi', 'price' => 22000],
            ['name' => 'Cappuccino', 'category' => 'Kopi', 'price' => 28000],
            ['name' => 'Cafe Latte', 'category' => 'Kopi', 'price' => 30000],
            ['name' => 'Caramel Latte', 'category' => 'Kopi', 'price' => 33000],
            ['name' => 'Mocha', 'category' => 'Kopi', 'price' => 32000],
            ['name' => 'Kopi Susu Gula Aren', 'category' => 'Kopi', 'price' => 30000],
            ['name' => 'Affogato', 'category' => 'Kopi', 'price' => 35000],
            ['name' => 'Chocolate', 'category' => 'Non Kopi', 'price' => 26000],
            ['name' => 'Matcha Latte', 'category' => 'Non Kopi', 'price' => 29000],
            ['name' => 'Red Velvet Latte', 'category' => 'Non Kopi', 'price' => 29000],
            ['name' => 'Fresh Milk', 'category' => 'Non Kopi', 'price' => 22000],
            ['name' => 'Lychee Tea', 'category' => 'Tea', 'price' => 24000],
            ['name' => 'Lemon Tea', 'category' => 'Tea', 'price' => 22000],
            ['name' => 'Jasmine Tea', 'category' => 'Tea', 'price' => 20000],
            ['name' => 'French Fries', 'category' => 'Snack', 'price' => 23000],
            ['name' => 'Chicken Wings', 'category' => 'Snack', 'price' => 32000],
            ['name' => 'Onion Rings', 'category' => 'Snack', 'price' => 24000],
            ['name' => 'Nasi Goreng Special', 'category' => 'Main Course', 'price' => 38000],
            ['name' => 'Mie Goreng Special', 'category' => 'Main Course', 'price' => 36000],
        ];

        foreach ($products as $item) {
            $category = $categories->get($item['category']) ?? $categories->first();

            Product::updateOrCreate(
                [
                    'outlet_id' => $outlet->id,
                    'name' => $item['name'],
                ],
                [
                    'category_id' => $category->id,
                    'description' => $item['name'] . ' pilihan terbaik Solusi Kopi',
                    'price' => $item['price'],
                    'image_url' => null,
                    'is_available' => true,
                ]
            );
        }
    }
}

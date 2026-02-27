<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;
use Carbon\Carbon;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $promotions = [
            [
                'code' => 'HEMAT10',
                'name' => 'Diskon 10 Persen',
                'description' => 'Diskon 10% untuk pembelian minimal 50.000',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'min_order_amount' => 50000,
                'status' => 'active',
            ],
            [
                'code' => 'NGOPI20K',
                'name' => 'Potongan 20 Ribu',
                'description' => 'Potongan langsung 20.000 untuk pembelian minimal 120.000',
                'discount_type' => 'fixed',
                'discount_value' => 20000,
                'min_order_amount' => 120000,
                'status' => 'active',
            ],
            [
                'code' => 'WEEKEND5',
                'name' => 'Promo Weekend',
                'description' => 'Diskon 5% akhir pekan',
                'discount_type' => 'percentage',
                'discount_value' => 5,
                'min_order_amount' => 30000,
                'status' => 'active',
            ],
        ];

        foreach ($promotions as $promo) {
            Promotion::updateOrCreate(
                ['code' => $promo['code']],
                [
                    'name' => $promo['name'],
                    'description' => $promo['description'],
                    'discount_type' => $promo['discount_type'],
                    'discount_value' => $promo['discount_value'],
                    'min_order_amount' => $promo['min_order_amount'],
                    'start_date' => Carbon::now()->subMonth(),
                    'end_date' => Carbon::now()->addYear(),
                    'status' => $promo['status'],
                ]
            );
        }
    }
}

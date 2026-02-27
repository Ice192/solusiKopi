<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Table;
use App\Models\Outlet;

class TableSeeder extends Seeder
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

        for ($i = 1; $i <= 10; $i++) {
            $number = str_pad((string) $i, 2, '0', STR_PAD_LEFT);

            Table::updateOrCreate(
                ['table_number' => $number],
                [
                    'outlet_id' => $outlet->id,
                    'table_code' => 'TB-' . $number,
                    'capacity' => 4,
                    'status' => 'available',
                    'qr_code_url' => null,
                ]
            );
        }
    }
}

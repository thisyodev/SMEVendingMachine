<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CashUnit;

class CashUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [1, 5, 10, 20, 50, 100, 500, 1000];

        foreach ($units as $denomination) {
            CashUnit::create([
                'denomination' => $denomination,
                'quantity' => 10 // mock เริ่มต้น
            ]);
        }
    }
}

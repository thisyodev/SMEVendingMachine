<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CashUnit;
use Illuminate\Support\Facades\DB;

class CashUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // ปิด FK ชั่วคราว
        DB::table('cash_units')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // เปิด FK กลับ

        DB::table('cash_units')->insert([
            ['denomination' => 1, 'quantity' => 100],
            ['denomination' => 5, 'quantity' => 100],
            ['denomination' => 10, 'quantity' => 100],
            ['denomination' => 20, 'quantity' => 100],
            ['denomination' => 50, 'quantity' => 100],
            ['denomination' => 100, 'quantity' => 100],
            ['denomination' => 500, 'quantity' => 50],
            ['denomination' => 1000, 'quantity' => 50],
        ]);
    }
}

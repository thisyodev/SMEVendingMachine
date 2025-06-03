<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->truncate();

        DB::table('products')->insert([
            ['name' => 'น้ำดื่ม', 'price' => 15, 'stock' => 20],
            ['name' => 'น้ำอัดลม', 'price' => 20, 'stock' => 15],
            ['name' => 'ขนมขบเคี้ยว', 'price' => 25, 'stock' => 10],
            ['name' => 'ช็อกโกแลต', 'price' => 30, 'stock' => 8],
            ['name' => 'กาแฟกระป๋อง', 'price' => 35, 'stock' => 12],
        ]);
    }
}

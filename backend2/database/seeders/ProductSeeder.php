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
        DB::table('products')->insert([
            ['name' => 'น้ำเปล่า', 'price' => 10, 'stock' => 20],
            ['name' => 'ขนมปัง', 'price' => 20, 'stock' => 15],
            ['name' => 'น้ำอัดลม', 'price' => 15, 'stock' => 10],
        ]);
    }
}

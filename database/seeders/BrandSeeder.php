<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Apple',
            'Samsung',
            'Sony',
            'Dell',
            'Logitech',
            'Anker',
            'Xiaomi',
        ];

        foreach ($brands as $name) {
            Brand::create(['name' => $name]);
        }
    }
}

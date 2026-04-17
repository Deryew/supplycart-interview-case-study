<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $brands = Brand::all()->keyBy('name');
        $categories = Category::all()->keyBy('name');

        $products = [
            ['name' => 'MacBook Air M3', 'price' => 549900, 'stock' => 15, 'brand' => 'Apple', 'category' => 'Laptops'],
            ['name' => 'MacBook Pro 14"', 'price' => 799900, 'stock' => 10, 'brand' => 'Apple', 'category' => 'Laptops'],
            ['name' => 'iPhone 16', 'price' => 419900, 'stock' => 25, 'brand' => 'Apple', 'category' => 'Smartphones'],
            ['name' => 'iPhone 16 Pro Max', 'price' => 619900, 'stock' => 20, 'brand' => 'Apple', 'category' => 'Smartphones'],
            ['name' => 'AirPods Pro 2', 'price' => 109900, 'stock' => 50, 'brand' => 'Apple', 'category' => 'Headphones'],
            ['name' => 'Galaxy S25 Ultra', 'price' => 569900, 'stock' => 18, 'brand' => 'Samsung', 'category' => 'Smartphones'],
            ['name' => 'Galaxy Tab S10', 'price' => 349900, 'stock' => 12, 'brand' => 'Samsung', 'category' => 'Laptops'],
            ['name' => 'Galaxy Buds 3 Pro', 'price' => 99900, 'stock' => 40, 'brand' => 'Samsung', 'category' => 'Headphones'],
            ['name' => 'Samsung 27" 4K Monitor', 'price' => 179900, 'stock' => 8, 'brand' => 'Samsung', 'category' => 'Monitors'],
            ['name' => 'Sony WH-1000XM5', 'price' => 159900, 'stock' => 30, 'brand' => 'Sony', 'category' => 'Headphones'],
            ['name' => 'Sony WF-1000XM5', 'price' => 129900, 'stock' => 35, 'brand' => 'Sony', 'category' => 'Headphones'],
            ['name' => 'Sony InZone M9 Monitor', 'price' => 299900, 'stock' => 5, 'brand' => 'Sony', 'category' => 'Monitors'],
            ['name' => 'Dell XPS 15', 'price' => 699900, 'stock' => 10, 'brand' => 'Dell', 'category' => 'Laptops'],
            ['name' => 'Dell UltraSharp 27" 4K', 'price' => 249900, 'stock' => 7, 'brand' => 'Dell', 'category' => 'Monitors'],
            ['name' => 'Logitech MX Master 3S', 'price' => 44900, 'stock' => 60, 'brand' => 'Logitech', 'category' => 'Mice'],
            ['name' => 'Logitech MX Keys S', 'price' => 49900, 'stock' => 45, 'brand' => 'Logitech', 'category' => 'Keyboards'],
            ['name' => 'Logitech G Pro X Superlight', 'price' => 64900, 'stock' => 30, 'brand' => 'Logitech', 'category' => 'Mice'],
            ['name' => 'Logitech G915 TKL', 'price' => 89900, 'stock' => 20, 'brand' => 'Logitech', 'category' => 'Keyboards'],
            ['name' => 'Anker 65W USB-C Charger', 'price' => 13900, 'stock' => 100, 'brand' => 'Anker', 'category' => 'Accessories'],
            ['name' => 'Anker PowerCore 20000', 'price' => 19900, 'stock' => 80, 'brand' => 'Anker', 'category' => 'Accessories'],
            ['name' => 'Anker Soundcore Liberty 4', 'price' => 39900, 'stock' => 50, 'brand' => 'Anker', 'category' => 'Headphones'],
            ['name' => 'Xiaomi 14T Pro', 'price' => 249900, 'stock' => 22, 'brand' => 'Xiaomi', 'category' => 'Smartphones'],
            ['name' => 'Xiaomi Pad 6', 'price' => 149900, 'stock' => 15, 'brand' => 'Xiaomi', 'category' => 'Laptops'],
            ['name' => 'Xiaomi Monitor Light Bar', 'price' => 8900, 'stock' => 70, 'brand' => 'Xiaomi', 'category' => 'Accessories'],
            ['name' => 'Xiaomi Buds 5 Pro', 'price' => 59900, 'stock' => 40, 'brand' => 'Xiaomi', 'category' => 'Headphones'],
        ];

        foreach ($products as $data) {
            Product::create([
                'name' => $data['name'],
                'price' => $data['price'],
                'stock' => $data['stock'],
                'brand_id' => $brands[$data['brand']]->id,
                'category_id' => $categories[$data['category']]->id,
                'description' => "High-quality {$data['name']} from {$data['brand']}.",
            ]);
        }
    }
}

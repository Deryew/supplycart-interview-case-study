<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\UserPrice;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@supplycart.my',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $vip = User::create([
            'name' => 'VIP Customer',
            'email' => 'vip@supplycart.my',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $regular = User::create([
            'name' => 'Regular User',
            'email' => 'user@supplycart.my',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // VIP user gets discounted prices on some products
        $discountedProducts = Product::inRandomOrder()->limit(8)->get();

        foreach ($discountedProducts as $product) {
            UserPrice::create([
                'user_id' => $vip->id,
                'product_id' => $product->id,
                'price' => (int) ($product->price * 0.85), // 15% discount
            ]);
        }
    }
}

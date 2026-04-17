<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(500, 50000),
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
            'image_url' => null,
            'stock' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }
}

<?php

namespace Tests\Feature\Products;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\UserPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductListTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => now()]);
    }

    public function test_guests_cannot_view_products(): void
    {
        $response = $this->get('/products');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_products(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->get('/products');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Products/Index')
            ->has('products.data', 3)
            ->has('brands')
            ->has('categories')
        );
    }

    public function test_products_can_be_filtered_by_brand(): void
    {
        $brand = Brand::factory()->create();
        $otherBrand = Brand::factory()->create();
        Product::factory()->count(2)->create(['brand_id' => $brand->id]);
        Product::factory()->create(['brand_id' => $otherBrand->id]);

        $response = $this->actingAs($this->user)->get("/products?brand_id={$brand->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('products.data', 2)
        );
    }

    public function test_products_can_be_filtered_by_category(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(2)->create(['category_id' => $category->id]);
        Product::factory()->create();

        $response = $this->actingAs($this->user)->get("/products?category_id={$category->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('products.data', 2)
        );
    }

    public function test_products_can_be_searched_by_name(): void
    {
        Product::factory()->create(['name' => 'MacBook Pro']);
        Product::factory()->create(['name' => 'iPhone 16']);

        $response = $this->actingAs($this->user)->get('/products?search=MacBook');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('products.data', 1)
        );
    }

    public function test_inactive_products_are_not_shown(): void
    {
        Product::factory()->create(['is_active' => true]);
        Product::factory()->create(['is_active' => false]);

        $response = $this->actingAs($this->user)->get('/products');

        $response->assertInertia(fn ($page) => $page
            ->has('products.data', 1)
        );
    }

    public function test_user_specific_pricing_is_applied(): void
    {
        $product = Product::factory()->create(['price' => 10000]);
        UserPrice::create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'price' => 8500,
        ]);

        $response = $this->actingAs($this->user)->get('/products');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('products.data', 1)
            ->where('products.data.0.effectivePrice', 8500)
            ->where('products.data.0.hasDiscount', true)
        );
    }
}

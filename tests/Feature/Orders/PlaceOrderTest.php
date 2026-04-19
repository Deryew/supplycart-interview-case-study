<?php

namespace Tests\Feature\Orders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Models\UserPrice;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PlaceOrderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => now()]);

        // Mock the Stripe checkout session creation so tests don't call Stripe API
        $mockSession = Mockery::mock();
        $mockSession->id = 'cs_test_fake_session_id';
        $mockSession->url = '/orders';

        $realService = app(OrderService::class);
        $spy = Mockery::mock($realService)->makePartial();
        $spy->shouldReceive('createCheckoutSession')->andReturn($mockSession);
        $this->app->instance(OrderService::class, $spy);
    }

    private function createCartWithItems(array $items = []): Cart
    {
        $cart = Cart::create(['user_id' => $this->user->id, 'is_active' => true]);

        if (empty($items)) {
            $product = Product::factory()->create(['price' => 10000, 'stock' => 50]);
            CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 2]);
        } else {
            foreach ($items as $item) {
                CartItem::create(['cart_id' => $cart->id, ...$item]);
            }
        }

        return $cart;
    }

    public function test_user_can_place_order(): void
    {
        $this->createCartWithItems();

        $response = $this->actingAs($this->user)->post('/orders', [
            'notes' => 'Please deliver fast',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total_amount' => 20000,
            'status' => 'pending',
            'notes' => 'Please deliver fast',
        ]);

        $this->assertDatabaseHas('order_items', [
            'quantity' => 2,
            'unit_price' => 10000,
            'total_price' => 20000,
        ]);
    }

    public function test_order_generates_unique_order_number(): void
    {
        $this->createCartWithItems();

        $this->actingAs($this->user)->post('/orders');

        $this->assertDatabaseCount('orders', 1);
        $order = $this->user->orders->first();
        $this->assertMatchesRegularExpression('/^ORD-\d{8}-[A-Z0-9]{4}$/', $order->order_number);
    }

    public function test_cart_is_deactivated_after_order(): void
    {
        $cart = $this->createCartWithItems();

        $this->actingAs($this->user)->post('/orders');

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'is_active' => false,
        ]);
    }

    public function test_stock_is_decremented_after_order(): void
    {
        $product = Product::factory()->create(['price' => 5000, 'stock' => 10]);
        $this->createCartWithItems([
            ['product_id' => $product->id, 'quantity' => 3],
        ]);

        $this->actingAs($this->user)->post('/orders');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 7,
        ]);
    }

    public function test_cannot_place_order_with_empty_cart(): void
    {
        $response = $this->actingAs($this->user)->post('/orders');

        $response->assertSessionHasErrors('cart');
    }

    public function test_cannot_place_order_with_insufficient_stock(): void
    {
        $product = Product::factory()->create(['price' => 5000, 'stock' => 2]);
        $this->createCartWithItems([
            ['product_id' => $product->id, 'quantity' => 5],
        ]);

        $response = $this->actingAs($this->user)->post('/orders');

        $response->assertSessionHasErrors('cart');
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_snapshots_price_at_time_of_order(): void
    {
        $product = Product::factory()->create(['price' => 10000, 'stock' => 50]);
        $this->createCartWithItems([
            ['product_id' => $product->id, 'quantity' => 1],
        ]);

        // Change price after adding to cart
        $product->update(['price' => 15000]);

        $this->actingAs($this->user)->post('/orders');

        // Order should have the new price (fetched at checkout time)
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'unit_price' => 15000,
        ]);
    }

    public function test_order_uses_user_specific_price(): void
    {
        $product = Product::factory()->create(['price' => 10000, 'stock' => 50]);
        UserPrice::create([
            'user_id' => $this->user->id,
            'product_id' => $product->id,
            'price' => 8500,
        ]);
        $this->createCartWithItems([
            ['product_id' => $product->id, 'quantity' => 1],
        ]);

        $this->actingAs($this->user)->post('/orders');

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'unit_price' => 8500,
            'total_price' => 8500,
        ]);

        $this->assertDatabaseHas('orders', [
            'total_amount' => 8500,
        ]);
    }
}

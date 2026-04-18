<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => now()]);
    }

    public function test_user_can_view_order_history(): void
    {
        Order::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get('/orders');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Orders/Index')
            ->has('orders.data', 3)
        );
    }

    public function test_user_can_only_see_own_orders(): void
    {
        Order::factory()->count(2)->create(['user_id' => $this->user->id]);
        Order::factory()->count(3)->create(); // other users

        $response = $this->actingAs($this->user)->get('/orders');

        $response->assertInertia(fn ($page) => $page
            ->has('orders.data', 2)
        );
    }

    public function test_user_can_view_order_detail(): void
    {
        $product = Product::factory()->create();
        $order = Order::factory()->create(['user_id' => $this->user->id]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 10000,
            'total_price' => 20000,
        ]);

        $response = $this->actingAs($this->user)->get("/orders/{$order->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Orders/Show')
            ->where('order.orderNumber', $order->order_number)
            ->where('order.items.0.productName', $product->name)
            ->where('order.items.0.quantity', 2)
        );
    }

    public function test_user_cannot_view_other_users_order(): void
    {
        $order = Order::factory()->create();

        $response = $this->actingAs($this->user)->get("/orders/{$order->id}");

        $response->assertForbidden();
    }

    public function test_guests_cannot_view_orders(): void
    {
        $this->get('/orders')->assertRedirect('/login');
    }
}

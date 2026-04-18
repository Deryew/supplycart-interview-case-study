<?php

namespace Tests\Feature\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->product = Product::factory()->create(['stock' => 50, 'price' => 10000]);
    }

    public function test_guests_cannot_access_cart(): void
    {
        $this->get('/cart')->assertRedirect('/login');
        $this->post('/cart/items')->assertRedirect('/login');
    }

    public function test_user_can_view_empty_cart(): void
    {
        $response = $this->actingAs($this->user)->get('/cart');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Cart/Show')
            ->where('cart', null)
        );
    }

    public function test_user_can_add_product_to_cart(): void
    {
        $response = $this->actingAs($this->user)->post('/cart/items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
    }

    public function test_adding_same_product_increments_quantity(): void
    {
        // Create an active cart with the product already in it
        $cart = Cart::create(['user_id' => $this->user->id, 'is_active' => true]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($this->user)->post('/cart/items', [
            'product_id' => $this->product->id,
            'quantity' => 3,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $this->product->id,
            'quantity' => 4,
        ]);
    }

    public function test_user_can_update_cart_item_quantity(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id, 'is_active' => true]);
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->patch("/cart/items/{$cartItem->id}", ['quantity' => 5]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 5,
        ]);
    }

    public function test_user_can_remove_cart_item(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id, 'is_active' => true]);
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->delete("/cart/items/{$cartItem->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    public function test_user_can_clear_cart(): void
    {
        $cart = Cart::create(['user_id' => $this->user->id, 'is_active' => true]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $this->product->id, 'quantity' => 1]);

        $response = $this->actingAs($this->user)->delete('/cart');

        $response->assertRedirect();
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_user_cannot_update_other_users_cart_item(): void
    {
        $otherUser = User::factory()->create(['email_verified_at' => now()]);
        $cart = Cart::create(['user_id' => $otherUser->id, 'is_active' => true]);
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->patch("/cart/items/{$cartItem->id}", ['quantity' => 5]);

        $response->assertForbidden();
    }

    public function test_add_to_cart_validates_product_exists(): void
    {
        $response = $this->actingAs($this->user)->post('/cart/items', [
            'product_id' => 9999,
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors('product_id');
    }

    public function test_add_to_cart_validates_quantity(): void
    {
        $response = $this->actingAs($this->user)->post('/cart/items', [
            'product_id' => $this->product->id,
            'quantity' => 0,
        ]);

        $response->assertSessionHasErrors('quantity');
    }
}

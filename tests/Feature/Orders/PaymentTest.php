<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email_verified_at' => now()]);
    }

    private function createOrder(array $attributes = []): Order
    {
        return Order::factory()->create([
            'user_id' => $this->user->id,
            ...$attributes,
        ]);
    }

    // --- Checkout Success ---

    public function test_checkout_success_marks_order_as_paid(): void
    {
        $order = $this->createOrder([
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->get("/checkout/{$order->id}/success?session_id=cs_test_123");

        $response->assertRedirect(route('orders.show', $order));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        $order->refresh();
        $this->assertNotNull($order->paid_at);
    }

    public function test_checkout_success_does_not_double_mark_paid_order(): void
    {
        $order = $this->createOrder([
            'payment_status' => 'paid',
            'status' => 'processing',
            'paid_at' => now()->subMinute(),
        ]);

        $originalPaidAt = $order->paid_at->toISOString();

        $this->actingAs($this->user)
            ->get("/checkout/{$order->id}/success?session_id=cs_test_123");

        $order->refresh();
        $this->assertEquals($originalPaidAt, $order->paid_at->toISOString());
    }

    public function test_checkout_success_unauthorized_for_other_users_order(): void
    {
        $otherUser = User::factory()->create(['email_verified_at' => now()]);
        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->get("/checkout/{$order->id}/success");

        $response->assertForbidden();
    }

    // --- Checkout Cancel ---

    public function test_checkout_cancel_redirects_without_modifying_order(): void
    {
        $order = $this->createOrder([
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->get("/checkout/{$order->id}/cancel");

        $response->assertRedirect(route('orders.show', $order));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);
    }

    // --- Retry Payment ---

    public function test_unpaid_order_can_retry_payment(): void
    {
        $order = $this->createOrder(['payment_status' => 'unpaid']);

        $mockSession = Mockery::mock();
        $mockSession->id = 'cs_test_new_session';
        $mockSession->url = 'https://checkout.stripe.com/test';

        $this->partialMock(OrderService::class, function ($mock) use ($mockSession) {
            $mock->shouldReceive('createCheckoutSession')->once()->andReturn($mockSession);
        });

        // Inertia::location returns 409 for Inertia requests, 302 for regular
        $response = $this->actingAs($this->user)
            ->post("/orders/{$order->id}/pay");

        $this->assertTrue(in_array($response->getStatusCode(), [302, 409]));
    }

    public function test_paid_order_cannot_retry_payment(): void
    {
        $order = $this->createOrder([
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->post("/orders/{$order->id}/pay");

        $response->assertRedirect(route('orders.show', $order));
        $response->assertSessionHas('error');
    }

    public function test_retry_payment_unauthorized_for_other_users_order(): void
    {
        $otherUser = User::factory()->create(['email_verified_at' => now()]);
        $order = Order::factory()->create([
            'user_id' => $otherUser->id,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->user)
            ->post("/orders/{$order->id}/pay");

        $response->assertForbidden();
    }

    // --- Stripe Webhook ---

    public function test_webhook_marks_order_as_paid(): void
    {
        $order = $this->createOrder([
            'payment_status' => 'unpaid',
            'stripe_checkout_session_id' => 'cs_test_webhook_123',
        ]);

        $payload = json_encode([
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_webhook_123',
                ],
            ],
        ]);

        $response = $this->post('/stripe/webhook', [], [
            'CONTENT_TYPE' => 'application/json',
        ]);

        // Send raw payload since webhook reads raw content
        $response = $this->call('POST', '/stripe/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], $payload);

        $response->assertOk();
        $response->assertJson(['status' => 'ok']);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);
    }

    public function test_webhook_ignores_already_paid_order(): void
    {
        $order = $this->createOrder([
            'payment_status' => 'paid',
            'status' => 'processing',
            'stripe_checkout_session_id' => 'cs_test_already_paid',
            'paid_at' => now()->subMinute(),
        ]);

        $payload = json_encode([
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_test_already_paid',
                ],
            ],
        ]);

        $response = $this->call('POST', '/stripe/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], $payload);

        $response->assertOk();

        $order->refresh();
        $this->assertEquals('processing', $order->status);
    }

    public function test_webhook_ignores_unknown_event_types(): void
    {
        $payload = json_encode([
            'type' => 'payment_intent.created',
            'data' => [
                'object' => ['id' => 'pi_test_123'],
            ],
        ]);

        $response = $this->call('POST', '/stripe/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], $payload);

        $response->assertOk();
        $response->assertJson(['status' => 'ok']);
    }
}

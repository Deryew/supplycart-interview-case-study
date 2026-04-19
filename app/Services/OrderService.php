<?php

namespace App\Services;

use App\Events\OrderPlaced;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Models\UserPrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

class OrderService
{
    public function placeOrder(User $user, ?string $notes = null): Order
    {
        return DB::transaction(function () use ($user, $notes) {
            $cart = Cart::with('cartItems.product')
                ->where('user_id', $user->id)
                ->active()
                ->lockForUpdate()
                ->first();

            if (!$cart || $cart->cartItems->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Your cart is empty.',
                ]);
            }

            // Validate stock and build order items
            $orderItems = [];
            $totalAmount = 0;

            foreach ($cart->cartItems as $cartItem) {
                $product = $cartItem->product;

                if (!$product->is_active) {
                    throw ValidationException::withMessages([
                        'cart' => "Product '{$product->name}' is no longer available.",
                    ]);
                }

                if ($product->stock < $cartItem->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "Insufficient stock for '{$product->name}'. Available: {$product->stock}.",
                    ]);
                }

                $unitPrice = $this->resolvePrice($user, $product);
                $lineTotal = $unitPrice * $cartItem->quantity;
                $totalAmount += $lineTotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $lineTotal,
                ];

                $product->decrement('stock', $cartItem->quantity);
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'notes' => $notes,
            ]);

            $order->orderItems()->createMany($orderItems);

            // Deactivate cart
            $cart->update(['is_active' => false]);

            $order->load('orderItems');

            OrderPlaced::dispatch($order);

            return $order;
        });
    }

    public function createCheckoutSession(Order $order)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItems = $order->orderItems->map(fn ($item) => [
            'price_data' => [
                'currency' => 'myr',
                'product_data' => [
                    'name' => $item->product_name,
                ],
                'unit_amount' => $item->unit_price,
            ],
            'quantity' => $item->quantity,
        ])->toArray();

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success', $order) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel', $order),
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
        ]);

        $order->update(['stripe_checkout_session_id' => $session->id]);

        return $session;
    }

    public function markAsPaid(Order $order): void
    {
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
            'paid_at' => now(),
        ]);
    }

    private function resolvePrice(User $user, $product): int
    {
        $userPrice = UserPrice::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        return $userPrice?->price ?? $product->price;
    }
}

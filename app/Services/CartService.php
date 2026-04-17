<?php

namespace App\Services;

use App\Events\ProductAddedToCart;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getOrCreateActiveCart(User $user): Cart
    {
        return $user->activeCart ?? Cart::create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);
    }

    public function getCartWithItems(User $user): ?Cart
    {
        return Cart::with(['cartItems.product.brand', 'cartItems.product.category'])
            ->where('user_id', $user->id)
            ->active()
            ->first();
    }

    public function addItem(User $user, int $productId, int $quantity = 1): CartItem
    {
        return DB::transaction(function () use ($user, $productId, $quantity) {
            $cart = $this->getOrCreateActiveCart($user);

            $cartItem = $cart->cartItems()->where('product_id', $productId)->first();

            if ($cartItem) {
                $cartItem->update(['quantity' => $cartItem->quantity + $quantity]);
            } else {
                $cartItem = $cart->cartItems()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }

            $cartItem->load('product');

            ProductAddedToCart::dispatch($user, $cartItem);

            return $cartItem;
        });
    }

    public function updateItemQuantity(CartItem $cartItem, int $quantity): CartItem
    {
        $cartItem->update(['quantity' => $quantity]);

        return $cartItem->load('product');
    }

    public function removeItem(CartItem $cartItem): void
    {
        $cartItem->delete();
    }

    public function clearCart(User $user): void
    {
        $cart = $user->activeCart;

        if ($cart) {
            $cart->cartItems()->delete();
        }
    }
}

<?php

namespace App\Services;

use App\Events\ProductAddedToCart;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
            $product = Product::find($productId);

            if (!$product || !$product->is_active) {
                throw ValidationException::withMessages([
                    'product_id' => 'This product is not available.',
                ]);
            }

            if ($product->stock < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => "Only {$product->stock} items available in stock.",
                ]);
            }

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

            Cache::forget("cart_count:{$user->id}");

            ProductAddedToCart::dispatch($user, $cartItem);

            return $cartItem;
        });
    }

    public function updateItemQuantity(CartItem $cartItem, int $quantity): CartItem
    {
        $cartItem->update(['quantity' => $quantity]);

        Cache::forget("cart_count:{$cartItem->cart->user_id}");

        return $cartItem->load('product');
    }

    public function removeItem(CartItem $cartItem): void
    {
        $userId = $cartItem->cart->user_id;

        $cartItem->delete();

        Cache::forget("cart_count:{$userId}");
    }

    public function clearCart(User $user): void
    {
        $cart = $user->activeCart;

        if ($cart) {
            $cart->cartItems()->delete();
        }

        Cache::forget("cart_count:{$user->id}");
    }
}

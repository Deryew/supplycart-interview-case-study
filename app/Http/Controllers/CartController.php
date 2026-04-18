<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService,
    ) {}

    public function show(): Response
    {
        $cart = $this->cartService->getCartWithItems(auth()->user());

        return Inertia::render('Cart/Show', [
            'cart' => $cart && $cart->cartItems->isNotEmpty()
                ? (new CartResource($cart))->resolve()
                : null,
        ]);
    }

    public function addItem(AddToCartRequest $request): RedirectResponse
    {
        $this->cartService->addItem(
            $request->user(),
            $request->validated('product_id'),
            $request->validated('quantity'),
        );

        return back()->with('success', 'Product added to cart.');
    }

    public function updateItem(UpdateCartItemRequest $request, CartItem $cartItem): RedirectResponse
    {
        $this->authorize('update', $cartItem);

        $this->cartService->updateItemQuantity(
            $cartItem,
            $request->validated('quantity'),
        );

        return back()->with('success', 'Cart updated.');
    }

    public function removeItem(CartItem $cartItem): RedirectResponse
    {
        $this->authorize('delete', $cartItem);

        $this->cartService->removeItem($cartItem);

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear(): RedirectResponse
    {
        $this->cartService->clearCart(auth()->user());

        return back()->with('success', 'Cart cleared.');
    }
}

<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Events\ProductAddedToCart;
use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogActivity
{
    public function __construct(
        private ActivityLogService $activityLogService,
    ) {}

    public function handleLogin(Login $event): void
    {
        $this->activityLogService->log(
            $event->user,
            'login',
            'User logged in',
            null,
            request()->ip(),
        );
    }

    public function handleLogout(Logout $event): void
    {
        $this->activityLogService->log(
            $event->user,
            'logout',
            'User logged out',
            null,
            request()->ip(),
        );
    }

    public function handleAddToCart(ProductAddedToCart $event): void
    {
        $this->activityLogService->log(
            $event->user,
            'add_to_cart',
            "Added {$event->cartItem->product->name} to cart",
            [
                'product_id' => $event->cartItem->product_id,
                'quantity' => $event->cartItem->quantity,
            ],
            request()->ip(),
        );
    }

    public function handleOrderPlaced(OrderPlaced $event): void
    {
        $this->activityLogService->log(
            $event->order->user,
            'place_order',
            "Placed order {$event->order->order_number}",
            [
                'order_id' => $event->order->id,
                'order_number' => $event->order->order_number,
                'total_amount' => $event->order->total_amount,
            ],
            request()->ip(),
        );
    }
}

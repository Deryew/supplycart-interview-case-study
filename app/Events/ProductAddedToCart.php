<?php

namespace App\Events;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductAddedToCart
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user,
        public CartItem $cartItem,
    ) {}
}

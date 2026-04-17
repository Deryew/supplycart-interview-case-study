<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Events\ProductAddedToCart;
use App\Listeners\LogActivity;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            [LogActivity::class, 'handleLogin'],
        ],
        Logout::class => [
            [LogActivity::class, 'handleLogout'],
        ],
        ProductAddedToCart::class => [
            [LogActivity::class, 'handleAddToCart'],
        ],
        OrderPlaced::class => [
            [LogActivity::class, 'handleOrderPlaced'],
        ],
    ];
}

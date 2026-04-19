<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'cartItemCount' => fn () => $request->user()
                ? (int) Cache::remember(
                    "cart_count:{$request->user()->id}",
                    60,
                    fn () => \App\Models\CartItem::whereHas('cart', fn ($q) => $q
                        ->where('user_id', $request->user()->id)
                        ->where('is_active', true)
                    )->sum('quantity')
                )
                : 0,
        ];
    }
}

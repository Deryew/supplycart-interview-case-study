<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
    ) {}

    public function index(): Response
    {
        $orders = auth()->user()
            ->orders()
            ->with('orderItems')
            ->latest()
            ->paginate(15);

        return Inertia::render('Orders/Index', [
            'orders' => $orders->through(fn ($order) => (new OrderResource($order))->resolve()),
        ]);
    }

    public function show(Order $order): Response
    {
        $this->authorize('view', $order);

        $order->load('orderItems');

        return Inertia::render('Orders/Show', [
            'order' => (new OrderResource($order))->resolve(),
        ]);
    }

    public function store(PlaceOrderRequest $request): \Symfony\Component\HttpFoundation\Response
    {
        $order = $this->orderService->placeOrder(
            $request->user(),
            $request->validated('notes'),
        );

        $session = $this->orderService->createCheckoutSession($order);

        return Inertia::location($session->url);
    }

    public function retryPayment(Order $order): \Symfony\Component\HttpFoundation\Response
    {
        $this->authorize('view', $order);

        if ($order->payment_status === 'paid') {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'This order has already been paid.');
        }

        $session = $this->orderService->createCheckoutSession($order);

        return Inertia::location($session->url);
    }

    public function checkoutSuccess(Order $order): RedirectResponse
    {
        $this->authorize('view', $order);

        if ($order->payment_status !== 'paid') {
            $this->orderService->markAsPaid($order);
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Payment successful! Your order has been placed.');
    }

    public function checkoutCancel(Order $order): RedirectResponse
    {
        $this->authorize('view', $order);

        return redirect()
            ->route('orders.show', $order)
            ->with('error', 'Payment was cancelled. You can retry from your order page.');
    }
}

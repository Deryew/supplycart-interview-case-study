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
            'orders' => OrderResource::collection($orders),
        ]);
    }

    public function show(Order $order): Response
    {
        $this->authorize('view', $order);

        $order->load('orderItems');

        return Inertia::render('Orders/Show', [
            'order' => new OrderResource($order),
        ]);
    }

    public function store(PlaceOrderRequest $request): RedirectResponse
    {
        $order = $this->orderService->placeOrder(
            $request->user(),
            $request->validated('notes'),
        );

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order placed successfully!');
    }
}

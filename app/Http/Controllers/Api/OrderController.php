<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateOrder;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = Order::query()
            ->with('customer')
            ->withCount('items')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(fn ($q) => $q
                    ->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%")));
            })
            ->latest()
            ->paginate(min($request->integer('per_page', 15), 100));

        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request, CreateOrder $createOrder): OrderResource
    {
        $order = $createOrder->handle($request->validated());

        return new OrderResource($order->load(['customer', 'items']));
    }

    public function show(Order $order): OrderResource
    {
        return new OrderResource($order->load(['customer', 'items']));
    }

    public function updateStatus(Request $request, Order $order): OrderResource|JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(OrderStatus::class)],
        ]);

        if (! $order->transitionTo(OrderStatus::from($validated['status']))) {
            return response()->json([
                'message' => __('Invalid status transition.'),
            ], 422);
        }

        return new OrderResource($order->fresh()->load(['customer', 'items']));
    }
}

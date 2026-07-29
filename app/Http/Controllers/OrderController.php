<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
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
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function create(): View
    {
        return view('orders.create', [
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'products' => Product::where('is_active', true)
                ->where('stock', '>', 0)
                ->orderBy('name')
                ->get(['id', 'name', 'price', 'stock']),
        ]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $order = DB::transaction(function () use ($request) {
            $order = Order::create([
                'customer_id' => $request->validated('customer_id'),
                'status' => OrderStatus::Pending,
                'notes' => $request->validated('notes'),
            ]);

            $total = 0;

            foreach ($request->validated('items') as $line) {
                $product = Product::query()->lockForUpdate()->findOrFail($line['product_id']);

                if ($product->stock < $line['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => __('Insufficient stock for :product.', ['product' => $product->name]),
                    ]);
                }

                $product->decrement('stock', $line['quantity']);

                $subtotal = round($product->price * $line['quantity'], 2);
                $total += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $line['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total' => $total]);

            return $order;
        });

        return redirect()->route('orders.show', $order)->with('success', __('Order created.'));
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'items']);

        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::enum(OrderStatus::class)],
        ]);

        $target = OrderStatus::from($validated['status']);

        if (! $order->status->canTransitionTo($target)) {
            return back()->with('error', __('Invalid status transition.'));
        }

        DB::transaction(function () use ($order, $target) {
            // 取消訂單時將品項數量歸還庫存
            if ($target === OrderStatus::Cancelled) {
                foreach ($order->items as $item) {
                    Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => $target]);
        });

        return back()->with('success', __('Order status updated.'));
    }
}

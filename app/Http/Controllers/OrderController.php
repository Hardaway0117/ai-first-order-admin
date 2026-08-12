<?php

namespace App\Http\Controllers;

use App\Actions\CreateOrder;
use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $this->filteredOrders($request)
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function export(Request $request): StreamedResponse
    {
        return response()->streamDownload(function () use ($request) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM：Excel 直接開啟中文才不會亂碼
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, [__('Order Number'), __('Customer'), __('Items Count'), __('Total'), __('Status'), __('Created At')]);

            $this->filteredOrders($request)->lazy()->each(function (Order $order) use ($out) {
                fputcsv($out, [
                    $order->order_number,
                    $order->customer->name,
                    $order->items_count,
                    $order->total,
                    $order->status->label(),
                    $order->created_at->format('Y-m-d H:i'),
                ]);
            });

            fclose($out);
        }, 'orders-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** 列表與匯出共用同一組篩選條件，兩邊結果永遠一致 */
    private function filteredOrders(Request $request): Builder
    {
        return Order::query()
            ->with('customer')
            ->withCount('items')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(fn ($q) => $q
                    ->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%")));
            })
            ->latest();
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

    public function store(StoreOrderRequest $request, CreateOrder $createOrder): RedirectResponse
    {
        $order = $createOrder->handle($request->validated());

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

        if (! $order->transitionTo(OrderStatus::from($validated['status']))) {
            return back()->with('error', __('Invalid status transition.'));
        }

        return back()->with('success', __('Order status updated.'));
    }
}

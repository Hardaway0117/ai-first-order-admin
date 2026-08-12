<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'products' => Product::count(),
            'customers' => Customer::count(),
            'orders' => Order::count(),
            'pending_orders' => Order::where('status', OrderStatus::Pending)->count(),
        ];

        // 最久未處理的訂單排前面：等最久的最緊急
        $pendingOrders = Order::with('customer')
            ->where('status', OrderStatus::Pending)
            ->oldest()
            ->take(5)
            ->get();

        // 近 30 日營收趨勢（不含已取消；沒訂單的日期補 0，日期軸才連續）
        $since = now()->subDays(29)->startOfDay();
        $revenueByDay = Order::where('created_at', '>=', $since)
            ->where('status', '!=', OrderStatus::Cancelled)
            ->selectRaw('DATE(created_at) as day, SUM(total) as revenue')
            ->groupBy('day')
            ->pluck('revenue', 'day');

        $trend = ['labels' => [], 'revenue' => []];
        for ($day = $since->copy(); $day->lte(now()); $day->addDay()) {
            $trend['labels'][] = $day->format('m/d');
            $trend['revenue'][] = round((float) ($revenueByDay[$day->format('Y-m-d')] ?? 0), 2);
        }

        // 訂單狀態分佈，沿用狀態徽章色系
        $countByStatus = Order::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusChart = ['labels' => [], 'counts' => [], 'colors' => []];
        foreach (OrderStatus::cases() as $status) {
            $statusChart['labels'][] = $status->label();
            $statusChart['counts'][] = (int) ($countByStatus[$status->value] ?? 0);
            $statusChart['colors'][] = $status->chartColor();
        }

        return view('dashboard', compact('stats', 'pendingOrders', 'trend', 'statusChart'));
    }
}

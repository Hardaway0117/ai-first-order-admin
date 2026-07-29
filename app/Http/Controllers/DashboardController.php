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

        return view('dashboard', compact('stats'));
    }
}

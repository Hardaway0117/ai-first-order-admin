<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 第一階段以佔位數值呈現，待商品／客戶／訂單模組完成後改為實際統計查詢。
        $stats = [
            'products' => 0,
            'customers' => 0,
            'orders' => 0,
            'pending_orders' => 0,
        ];

        return view('dashboard', compact('stats'));
    }
}

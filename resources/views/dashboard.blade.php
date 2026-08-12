<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">{{ __('Dashboard') }}</h1>
    </x-slot>

    <p class="text-secondary mb-4">{{ __('Welcome back, :name! This is the :app admin panel.', ['name' => Auth::user()->name, 'app' => config('app.name')]) }}</p>

    @php
        // 統計卡自製線條圖示（stroke 用 currentColor，顏色跟著 text-{tint} 走）
        $tileIcons = [
            'products' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8 12 3 3 8v8l9 5 9-5V8z"/><path d="m3 8 9 5 9-5"/><path d="M12 13v8"/></svg>',
            'customers' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8.5" r="3.5"/><path d="M2.5 20c.9-3.1 3.4-5 6.5-5s5.6 1.9 6.5 5"/><circle cx="17.5" cy="9.5" r="2.5"/><path d="M16.8 15.2c2.2.5 3.9 2 4.7 4.3"/></svg>',
            'orders' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12v18l-2-1.6L14 21l-2-1.6L10 21l-2-1.6L6 21V3z"/><path d="M9.5 8.5h5M9.5 12.5h5"/></svg>',
            'pending' => '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/></svg>',
        ];
    @endphp

    <div class="row g-3">
        @foreach ([
            ['label' => __('Total Products'), 'value' => $stats['products'], 'icon' => $tileIcons['products'], 'tint' => 'primary', 'url' => route('products.index')],
            ['label' => __('Total Customers'), 'value' => $stats['customers'], 'icon' => $tileIcons['customers'], 'tint' => 'success', 'url' => route('customers.index')],
            ['label' => __('Total Orders'), 'value' => $stats['orders'], 'icon' => $tileIcons['orders'], 'tint' => 'info', 'url' => route('orders.index')],
            ['label' => __('Pending Orders'), 'value' => $stats['pending_orders'], 'icon' => $tileIcons['pending'], 'tint' => 'warning', 'url' => route('orders.index', ['status' => \App\Enums\OrderStatus::Pending->value])],
        ] as $card)
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ $card['url'] }}" class="text-decoration-none text-reset d-block h-100">
                    <div class="card stat-card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-tile bg-{{ $card['tint'] }}-subtle text-{{ $card['tint'] }}">{!! $card['icon'] !!}</div>
                            <div>
                                <div class="text-secondary small">{{ $card['label'] }}</div>
                                <div class="fs-2 fw-bold lh-1 mt-1">{{ $card['value'] }}</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12 col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 mb-3"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1 text-primary" style="vertical-align:-3px"><path d="m3 17 5.2-5.2 3.6 3.6L19 8"/><path d="M14.5 8H19v4.5"/></svg>{{ __('Revenue trend (last 30 days)') }}</h2>
                    <div style="height: 260px">
                        <canvas id="revenueTrendChart"
                            data-labels='@json($trend['labels'])'
                            data-values='@json($trend['revenue'])'
                            data-label="{{ __('Daily revenue') }}"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 mb-3"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1 text-primary" style="vertical-align:-3px"><path d="M5 20v-6M12 20V6M19 20v-9"/><path d="M3 20h18"/></svg>{{ __('Order status distribution') }}</h2>
                    <div style="height: 260px">
                        <canvas id="statusChart"
                            data-labels='@json($statusChart['labels'])'
                            data-values='@json($statusChart['counts'])'
                            data-colors='@json($statusChart['colors'])'></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($pendingOrders->isNotEmpty())
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 mb-0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1 text-warning" style="vertical-align:-3px"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/></svg>{{ __('Oldest pending orders') }}</h2>
                    <a href="{{ route('orders.index', ['status' => \App\Enums\OrderStatus::Pending->value]) }}" class="small text-decoration-none">{{ __('View all') }}</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Order Number') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th class="text-end">{{ __('Total') }}</th>
                                <th class="text-end">{{ __('Created At') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingOrders as $order)
                                <tr>
                                    <td class="font-monospace">{{ $order->order_number }}</td>
                                    <td>{{ $order->customer->name }}</td>
                                    <td class="text-end">${{ number_format($order->total, 2) }}</td>
                                    <td class="text-end text-secondary">{{ $order->created_at->format('m/d') }}・{{ __('Waiting :days days', ['days' => (int) $order->created_at->diffInDays(now())]) }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">{{ __('View') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>

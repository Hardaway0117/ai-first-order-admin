<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">{{ __('Dashboard') }}</h1>
    </x-slot>

    <p class="text-secondary mb-4">{{ __('Welcome back, :name! This is the :app admin panel.', ['name' => Auth::user()->name, 'app' => config('app.name')]) }}</p>

    <div class="row g-3">
        @foreach ([
            ['label' => __('Total Products'), 'value' => $stats['products'], 'icon' => '📦', 'tint' => 'primary', 'url' => route('products.index')],
            ['label' => __('Total Customers'), 'value' => $stats['customers'], 'icon' => '👥', 'tint' => 'success', 'url' => route('customers.index')],
            ['label' => __('Total Orders'), 'value' => $stats['orders'], 'icon' => '🧾', 'tint' => 'info', 'url' => route('orders.index')],
            ['label' => __('Pending Orders'), 'value' => $stats['pending_orders'], 'icon' => '⏳', 'tint' => 'warning', 'url' => route('orders.index', ['status' => \App\Enums\OrderStatus::Pending->value])],
        ] as $card)
            <div class="col-12 col-sm-6 col-xl-3">
                <a href="{{ $card['url'] }}" class="text-decoration-none text-reset d-block h-100">
                    <div class="card stat-card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-tile bg-{{ $card['tint'] }}-subtle">{{ $card['icon'] }}</div>
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
                    <h2 class="h6 mb-3">📈 {{ __('Revenue trend (last 30 days)') }}</h2>
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
                    <h2 class="h6 mb-3">📊 {{ __('Order status distribution') }}</h2>
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
                    <h2 class="h6 mb-0">⏳ {{ __('Oldest pending orders') }}</h2>
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

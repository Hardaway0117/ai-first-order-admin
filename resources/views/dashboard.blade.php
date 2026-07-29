<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">{{ __('Dashboard') }}</h1>
    </x-slot>

    <p class="text-secondary mb-4">{{ __('Welcome back, :name! This is the :app admin panel.', ['name' => Auth::user()->name, 'app' => config('app.name')]) }}</p>

    <div class="row g-3">
        @foreach ([
            ['label' => __('Total Products'), 'value' => $stats['products'], 'icon' => '📦', 'tint' => 'primary'],
            ['label' => __('Total Customers'), 'value' => $stats['customers'], 'icon' => '👥', 'tint' => 'success'],
            ['label' => __('Total Orders'), 'value' => $stats['orders'], 'icon' => '🧾', 'tint' => 'info'],
            ['label' => __('Pending Orders'), 'value' => $stats['pending_orders'], 'icon' => '⏳', 'tint' => 'warning'],
        ] as $card)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card stat-card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="stat-tile bg-{{ $card['tint'] }}-subtle">{{ $card['icon'] }}</div>
                        <div>
                            <div class="text-secondary small">{{ $card['label'] }}</div>
                            <div class="fs-2 fw-bold lh-1 mt-1">{{ $card['value'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>

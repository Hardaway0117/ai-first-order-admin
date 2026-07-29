<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">{{ __('Dashboard') }}</h1>
    </x-slot>

    <p class="text-secondary mb-4">{{ __('Welcome back, :name! This is the :app admin panel.', ['name' => Auth::user()->name, 'app' => config('app.name')]) }}</p>

    <div class="row g-3">
        {{-- 統計數字為第一階段佔位資料，第二階段改為實際資料庫查詢 --}}
        @foreach ([
            ['label' => __('Total Products'), 'value' => $stats['products'], 'icon' => '📦', 'variant' => 'primary'],
            ['label' => __('Total Customers'), 'value' => $stats['customers'], 'icon' => '👥', 'variant' => 'success'],
            ['label' => __('Total Orders'), 'value' => $stats['orders'], 'icon' => '🧾', 'variant' => 'info'],
            ['label' => __('Pending Orders'), 'value' => $stats['pending_orders'], 'icon' => '⏳', 'variant' => 'warning'],
        ] as $card)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-secondary small">{{ $card['label'] }}</div>
                            <div class="fs-2 fw-bold">{{ $card['value'] }}</div>
                        </div>
                        <span class="fs-1">{{ $card['icon'] }}</span>
                    </div>
                    <div class="card-footer border-0 p-0">
                        <div class="bg-{{ $card['variant'] }}" style="height: 4px;"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>

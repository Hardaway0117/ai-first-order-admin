<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">儀表板</h1>
    </x-slot>

    <p class="text-secondary mb-4">歡迎回來，{{ Auth::user()->name }}！這裡是 {{ config('app.name') }} 管理後台。</p>

    <div class="row g-3">
        {{-- 統計數字為第一階段佔位資料，第二階段改為實際資料庫查詢 --}}
        @foreach ([
            ['label' => '商品總數', 'value' => $stats['products'], 'icon' => '📦', 'variant' => 'primary'],
            ['label' => '客戶總數', 'value' => $stats['customers'], 'icon' => '👥', 'variant' => 'success'],
            ['label' => '訂單總數', 'value' => $stats['orders'], 'icon' => '🧾', 'variant' => 'info'],
            ['label' => '待處理訂單', 'value' => $stats['pending_orders'], 'icon' => '⏳', 'variant' => 'warning'],
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

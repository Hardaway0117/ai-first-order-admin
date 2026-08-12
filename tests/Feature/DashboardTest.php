<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_view_the_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('儀表板');
        $response->assertSee($user->name);
        $response->assertSee('待處理訂單');
    }

    public function test_dashboard_shows_real_statistics(): void
    {
        $user = User::factory()->create();

        \App\Models\Product::factory(2)->create();
        \App\Models\Order::factory()->pending()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertViewHas('stats', fn (array $stats) => $stats['products'] === 2
            && $stats['customers'] === 1
            && $stats['orders'] === 1
            && $stats['pending_orders'] === 1);
        $response->assertViewHas('pendingOrders', fn ($orders) => $orders->count() === 1);
    }

    public function test_dashboard_provides_chart_data(): void
    {
        $user = User::factory()->create();

        // 明確指定建立時間：工廠預設會隨機落在過去日期，可能超出 30 天視窗
        \App\Models\Order::factory()->pending()->create(['total' => 150, 'created_at' => now()]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        // 折線圖：30 天份的標籤與數值，今天的營收要含這筆訂單
        $response->assertViewHas('trend', fn (array $trend) => count($trend['labels']) === 30
            && count($trend['revenue']) === 30
            && $trend['revenue'][29] >= 150.0);
        // 狀態分佈：五種狀態都要有資料點，待處理至少 1 筆
        $response->assertViewHas('statusChart', fn (array $chart) => count($chart['labels']) === 5
            && array_sum($chart['counts']) === 1
            && count($chart['colors']) === 5);
        $response->assertSee('revenueTrendChart', false);
        $response->assertSee('statusChart', false);
    }
}

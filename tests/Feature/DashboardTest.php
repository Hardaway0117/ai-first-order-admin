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
}

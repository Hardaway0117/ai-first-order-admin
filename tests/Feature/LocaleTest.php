<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_switch_to_a_supported_locale(): void
    {
        $response = $this->from('/login')->get('/locale/en');

        $response->assertRedirect('/login');
        $this->assertSame('en', session('locale'));
    }

    public function test_unsupported_locale_is_ignored(): void
    {
        $this->from('/login')->get('/locale/xx')->assertRedirect('/login');

        $this->assertNull(session('locale'));
    }

    public function test_locale_choice_is_persisted_on_the_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/locale/en');

        $this->assertSame('en', $user->fresh()->locale);
    }

    public function test_saved_locale_is_applied_on_a_new_session(): void
    {
        $user = User::factory()->create(['locale' => 'en']);

        $this->actingAs($user)->get('/dashboard')->assertSee('Pending Orders');
    }

    public function test_dashboard_renders_in_selected_locale(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->withSession(['locale' => 'en'])
            ->get('/dashboard')
            ->assertSee('Pending Orders');

        $this->actingAs($user)->withSession(['locale' => 'zh_CN'])
            ->get('/dashboard')
            ->assertSee('待处理订单');

        $this->actingAs($user)->withSession(['locale' => 'zh_TW'])
            ->get('/dashboard')
            ->assertSee('待處理訂單');
    }
}

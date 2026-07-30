<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_cannot_access_user_management(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->get('/users')->assertForbidden();
        $this->actingAs($staff)->post('/users', [])->assertForbidden();
    }

    public function test_admin_can_list_users(): void
    {
        $admin = User::factory()->create();
        $staff = User::factory()->staff()->create();

        $this->actingAs($admin)->get('/users')
            ->assertOk()
            ->assertSee($admin->email)
            ->assertSee($staff->email);
    }

    public function test_admin_can_create_a_staff_user(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->post('/users', [
            'name' => '新員工',
            'email' => 'new-staff@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'staff',
        ])->assertRedirect('/users');

        $created = User::where('email', 'new-staff@example.com')->first();
        $this->assertNotNull($created);
        $this->assertSame(UserRole::Staff, $created->role);
    }

    public function test_admin_can_promote_a_staff_user(): void
    {
        $admin = User::factory()->create();
        $staff = User::factory()->staff()->create();

        $this->actingAs($admin)->patch("/users/{$staff->id}", [
            'name' => $staff->name,
            'email' => $staff->email,
            'role' => 'admin',
        ])->assertRedirect('/users');

        $this->assertSame(UserRole::Admin, $staff->fresh()->role);
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->patch("/users/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'staff',
        ]);

        $this->assertSame(UserRole::Admin, $admin->fresh()->role);
    }

    public function test_admin_cannot_delete_themselves_but_can_delete_others(): void
    {
        $admin = User::factory()->create();
        $staff = User::factory()->staff()->create();

        $this->actingAs($admin)->delete("/users/{$admin->id}")->assertForbidden();
        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        $this->actingAs($admin)->delete("/users/{$staff->id}")->assertRedirect('/users');
        $this->assertDatabaseMissing('users', ['id' => $staff->id]);
    }
}

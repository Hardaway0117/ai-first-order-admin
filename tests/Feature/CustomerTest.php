<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_guests_cannot_access_customers(): void
    {
        $this->get('/customers')->assertRedirect('/login');
    }

    public function test_customers_are_listed_with_search(): void
    {
        Customer::factory()->create(['name' => '王小明', 'email' => 'ming@example.com']);
        Customer::factory()->create(['name' => '李美麗', 'email' => 'mei@example.com']);

        $this->actingAs($this->user)->get('/customers')
            ->assertOk()
            ->assertSee('王小明')
            ->assertSee('李美麗');

        $this->actingAs($this->user)->get('/customers?search=ming@')
            ->assertOk()
            ->assertSee('王小明')
            ->assertDontSee('李美麗');
    }

    public function test_a_customer_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post('/customers', [
            'name' => '測試客戶',
            'email' => 'test@example.com',
            'phone' => '0912-345-678',
            'address' => '台北市信義區',
        ]);

        $response->assertRedirect('/customers');
        $this->assertDatabaseHas('customers', ['email' => 'test@example.com']);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        Customer::factory()->create(['email' => 'dup@example.com']);

        $this->actingAs($this->user)->post('/customers', [
            'name' => '重複客戶',
            'email' => 'dup@example.com',
        ])->assertSessionHasErrors('email');

        $this->assertSame(1, Customer::where('email', 'dup@example.com')->count());
    }

    public function test_a_customer_can_be_updated(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($this->user)->patch("/customers/{$customer->id}", [
            'name' => '更新後的名字',
            'email' => $customer->email,
        ])->assertRedirect('/customers');

        $this->assertSame('更新後的名字', $customer->fresh()->name);
    }

    public function test_a_customer_without_orders_can_be_deleted(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($this->user)->delete("/customers/{$customer->id}")->assertRedirect('/customers');

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_a_customer_with_orders_cannot_be_deleted(): void
    {
        $customer = Customer::factory()->create();
        Order::factory()->for($customer)->create();

        $this->actingAs($this->user)->delete("/customers/{$customer->id}");

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }
}

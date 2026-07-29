<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_token_can_be_issued_with_valid_credentials(): void
    {
        User::factory()->create(['email' => 'api@example.com']);

        $response = $this->postJson('/api/v1/auth/token', [
            'email' => 'api@example.com',
            'password' => 'password',
            'device_name' => 'phpunit',
        ]);

        $response->assertCreated()->assertJsonStructure(['token', 'token_type']);
    }

    public function test_a_token_is_rejected_with_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'api@example.com']);

        $this->postJson('/api/v1/auth/token', [
            'email' => 'api@example.com',
            'password' => 'wrong-password',
            'device_name' => 'phpunit',
        ])->assertUnprocessable();
    }

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $this->getJson('/api/v1/products')->assertUnauthorized();
    }

    public function test_products_can_be_listed_with_a_token(): void
    {
        $user = User::factory()->create();
        Product::factory(3)->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure(['data' => [['id', 'name', 'sku', 'price', 'stock']], 'links', 'meta']);
    }

    public function test_staff_cannot_create_products_via_api(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff, 'sanctum')->postJson('/api/v1/products', [
            'name' => 'API 商品',
            'sku' => 'SKU-API01',
            'price' => 100,
            'stock' => 5,
        ])->assertForbidden();
    }

    public function test_admin_can_create_products_via_api(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin, 'sanctum')->postJson('/api/v1/products', [
            'name' => 'API 商品',
            'sku' => 'SKU-API01',
            'price' => 100,
            'stock' => 5,
        ])->assertCreated()->assertJsonPath('data.sku', 'SKU-API01');
    }

    public function test_an_order_can_be_created_via_api(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/orders', [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.total', '200.00')
            ->assertJsonCount(1, 'data.items');

        $this->assertSame(8, $product->fresh()->stock);
    }

    public function test_order_status_can_be_updated_via_api_with_state_machine_guard(): void
    {
        $user = User::factory()->create();
        $order = \App\Models\Order::factory()->pending()->create();

        $this->actingAs($user, 'sanctum')->patchJson("/api/v1/orders/{$order->id}/status", [
            'status' => OrderStatus::Completed->value,
        ])->assertUnprocessable();

        $this->actingAs($user, 'sanctum')->patchJson("/api/v1/orders/{$order->id}/status", [
            'status' => OrderStatus::Processing->value,
        ])->assertOk()->assertJsonPath('data.status', 'processing');
    }
}

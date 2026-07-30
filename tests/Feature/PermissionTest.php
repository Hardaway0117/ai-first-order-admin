<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_view_products_but_not_manage_them(): void
    {
        $staff = User::factory()->staff()->create();
        $product = Product::factory()->create();

        $this->actingAs($staff)->get('/products')->assertOk();
        $this->actingAs($staff)->get('/products/create')->assertForbidden();

        $this->actingAs($staff)->post('/products', [
            'name' => '越權商品',
            'sku' => 'SKU-HACK1',
            'price' => 1,
            'stock' => 1,
        ])->assertForbidden();

        $this->actingAs($staff)->delete("/products/{$product->id}")->assertForbidden();
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_staff_cannot_manage_customers(): void
    {
        $staff = User::factory()->staff()->create();
        $customer = Customer::factory()->create();

        $this->actingAs($staff)->get('/customers')->assertOk();
        $this->actingAs($staff)->post('/customers', [
            'name' => '越權客戶',
            'email' => 'hack@example.com',
        ])->assertForbidden();
        $this->actingAs($staff)->delete("/customers/{$customer->id}")->assertForbidden();
    }

    public function test_staff_can_update_products(): void
    {
        $staff = User::factory()->staff()->create();
        $product = Product::factory()->create(['stock' => 5]);

        $this->actingAs($staff)->patch("/products/{$product->id}", [
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => $product->price,
            'stock' => 99,
            'is_active' => 1,
        ])->assertRedirect('/products');

        $this->assertSame(99, $product->fresh()->stock);
    }

    public function test_staff_can_create_orders(): void
    {
        $staff = User::factory()->staff()->create();
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);

        $this->actingAs($staff)->post('/orders', [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ])->assertRedirect();

        $this->assertSame(1, $customer->orders()->count());
    }

    public function test_admin_can_manage_products(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->get('/products/create')->assertOk();
    }

    public function test_staff_ui_hides_admin_actions(): void
    {
        $staff = User::factory()->staff()->create();
        Product::factory()->create();

        $this->actingAs($staff)->get('/products')
            ->assertDontSee(__('New Product'))
            ->assertDontSee(__('Delete'));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_guests_cannot_access_products(): void
    {
        $this->get('/products')->assertRedirect('/login');
    }

    public function test_products_are_listed_with_search(): void
    {
        Product::factory()->create(['name' => '無線藍牙耳機', 'sku' => 'SKU-AA111']);
        Product::factory()->create(['name' => '機械式鍵盤', 'sku' => 'SKU-BB222']);

        $this->actingAs($this->user)->get('/products')
            ->assertOk()
            ->assertSee('SKU-AA111')
            ->assertSee('SKU-BB222');

        $this->actingAs($this->user)->get('/products?search=SKU-AA')
            ->assertOk()
            ->assertSee('SKU-AA111')
            ->assertDontSee('SKU-BB222');
    }

    public function test_products_can_be_filtered_by_status_and_sorted_by_price(): void
    {
        Product::factory()->create(['sku' => 'SKU-CHEAP', 'price' => 10, 'is_active' => true]);
        Product::factory()->create(['sku' => 'SKU-COSTLY', 'price' => 999, 'is_active' => true]);
        Product::factory()->create(['sku' => 'SKU-OFF', 'is_active' => false]);

        $this->actingAs($this->user)->get('/products?status=0')
            ->assertSee('SKU-OFF')
            ->assertDontSee('SKU-CHEAP');

        $this->actingAs($this->user)->get('/products?status=1&sort=price_asc')
            ->assertSeeInOrder(['SKU-CHEAP', 'SKU-COSTLY']);
    }

    public function test_a_product_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post('/products', [
            'name' => '測試商品',
            'sku' => 'SKU-TEST1',
            'price' => 199.99,
            'stock' => 10,
            'is_active' => 1,
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', ['sku' => 'SKU-TEST1', 'name' => '測試商品']);
    }

    public function test_duplicate_sku_is_rejected(): void
    {
        Product::factory()->create(['sku' => 'SKU-DUP01']);

        $response = $this->actingAs($this->user)->post('/products', [
            'name' => '重複商品',
            'sku' => 'SKU-DUP01',
            'price' => 100,
            'stock' => 1,
        ]);

        $response->assertSessionHasErrors('sku');
        $this->assertSame(1, Product::where('sku', 'SKU-DUP01')->count());
    }

    public function test_a_product_can_be_updated(): void
    {
        $product = Product::factory()->create(['stock' => 5]);

        $this->actingAs($this->user)->patch("/products/{$product->id}", [
            'name' => $product->name,
            'sku' => $product->sku,
            'price' => $product->price,
            'stock' => 99,
            'is_active' => 1,
        ])->assertRedirect('/products');

        $this->assertSame(99, $product->fresh()->stock);
    }

    public function test_a_product_can_be_deleted(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->user)->delete("/products/{$product->id}")->assertRedirect('/products');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_a_product_with_order_records_cannot_be_deleted(): void
    {
        $product = Product::factory()->create();
        OrderItem::factory()->for(Order::factory())->for($product)->create();

        $this->actingAs($this->user)->delete("/products/{$product->id}");

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }
}

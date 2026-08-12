<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_guests_cannot_access_orders(): void
    {
        $this->get('/orders')->assertRedirect('/login');
    }

    public function test_an_order_is_created_with_items_total_and_stock_deduction(): void
    {
        $customer = Customer::factory()->create();
        $productA = Product::factory()->create(['price' => 100, 'stock' => 10]);
        $productB = Product::factory()->create(['price' => 50.5, 'stock' => 5]);

        $response = $this->actingAs($this->user)->post('/orders', [
            'customer_id' => $customer->id,
            'notes' => '測試訂單',
            'items' => [
                ['product_id' => $productA->id, 'quantity' => 2],
                ['product_id' => $productB->id, 'quantity' => 1],
            ],
        ]);

        $order = Order::first();

        $response->assertRedirect("/orders/{$order->id}");
        $this->assertSame(2, $order->items()->count());
        $this->assertSame('250.50', (string) $order->total);
        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertStringStartsWith('ORD-', $order->order_number);
        $this->assertSame(8, $productA->fresh()->stock);
        $this->assertSame(4, $productB->fresh()->stock);
    }

    public function test_order_creation_fails_when_stock_is_insufficient(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock' => 1]);

        $this->actingAs($this->user)->post('/orders', [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
        ])->assertSessionHasErrors('items');

        $this->assertSame(0, Order::count());
        $this->assertSame(1, $product->fresh()->stock);
    }

    public function test_a_valid_status_transition_is_applied(): void
    {
        $order = Order::factory()->pending()->create();

        $this->actingAs($this->user)->patch("/orders/{$order->id}/status", [
            'status' => OrderStatus::Processing->value,
        ]);

        $this->assertSame(OrderStatus::Processing, $order->fresh()->status);
    }

    public function test_an_invalid_status_transition_is_rejected(): void
    {
        $order = Order::factory()->pending()->create();

        $this->actingAs($this->user)->patch("/orders/{$order->id}/status", [
            'status' => OrderStatus::Completed->value,
        ]);

        $this->assertSame(OrderStatus::Pending, $order->fresh()->status);
    }

    public function test_cancelling_an_order_restores_stock(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

        $this->actingAs($this->user)->post('/orders', [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3],
            ],
        ]);

        $this->assertSame(7, $product->fresh()->stock);

        $order = Order::first();

        $this->actingAs($this->user)->patch("/orders/{$order->id}/status", [
            'status' => OrderStatus::Cancelled->value,
        ]);

        $this->assertSame(OrderStatus::Cancelled, $order->fresh()->status);
        $this->assertSame(10, $product->fresh()->stock);
    }

    public function test_order_detail_shows_items(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

        $this->actingAs($this->user)->post('/orders', [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $order = Order::first();

        $this->actingAs($this->user)->get("/orders/{$order->id}")
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee($product->name)
            ->assertSee($customer->name);
    }

    public function test_guests_cannot_export_orders(): void
    {
        $this->get('/orders/export')->assertRedirect('/login');
    }

    public function test_orders_can_be_exported_as_csv(): void
    {
        $customer = Customer::factory()->create(['name' => '匯出測試客戶']);
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

        $this->actingAs($this->user)->post('/orders', [
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $order = Order::first();

        $response = $this->actingAs($this->user)->get('/orders/export');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.csv', $response->headers->get('Content-Disposition'));

        $csv = $response->streamedContent();
        $this->assertStringContainsString($order->order_number, $csv);
        $this->assertStringContainsString('匯出測試客戶', $csv);
        $this->assertStringContainsString('200', $csv);
    }

    public function test_csv_export_respects_status_filter(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

        foreach (range(1, 2) as $i) {
            $this->actingAs($this->user)->post('/orders', [
                'customer_id' => $customer->id,
                'items' => [
                    ['product_id' => $product->id, 'quantity' => 1],
                ],
            ]);
        }

        [$kept, $cancelled] = Order::all();
        $cancelled->transitionTo(OrderStatus::Cancelled);

        $csv = $this->actingAs($this->user)
            ->get('/orders/export?status=pending')
            ->streamedContent();

        $this->assertStringContainsString($kept->order_number, $csv);
        $this->assertStringNotContainsString($cancelled->order_number, $csv);
    }
}

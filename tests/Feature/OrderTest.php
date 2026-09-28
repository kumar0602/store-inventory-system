<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendOrderConfirmationEmail;
use Tests\TestCase;

class OrderTest extends TestCase {
    use RefreshDatabase;

    public function test_can_create_order_successfully_and_deducts_stock(): void {
        Queue::fake();

        $customer = Customer::factory()->create(['email' => 'test@example.com']);
        $product = Product::factory()->create(['stock_on_hand' => 10, 'price_per_unit' => 50, 'tax_percentage' => 10]);

        $response = $this->postJson('/api/orders', [
            'customer_name' => 'Test Customer',
            'customer_email' => 'test@example.com',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(201)
         ->assertJsonPath('data.grand_total', 110);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_on_hand' => 8
        ]);

        Queue::assertPushed(SendOrderConfirmationEmail::class);
    }

    public function test_cannot_create_order_with_insufficient_stock(): void {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock_on_hand' => 2]);

        $response = $this->postJson('/api/orders', [
            'customer_email' => $customer->email,
            'customer_name' => $customer->name,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5]
            ]
        ]);

        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Order creation failed']);

        // Stock remains unchanged
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_on_hand' => 2
        ]);
    }

    public function test_can_fetch_low_stock_products(): void {
        Product::factory()->create(['name' => 'Low Stock Item', 'stock_on_hand' => 2]);
        Product::factory()->create(['name' => 'High Stock Item', 'stock_on_hand' => 20]);

        $response = $this->getJson('/api/products/low-stock?threshold=5');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'products')
                 ->assertJsonFragment(['name' => 'Low Stock Item']);
    }
}
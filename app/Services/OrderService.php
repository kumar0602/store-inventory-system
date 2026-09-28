<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Jobs\SendOrderConfirmationEmail;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService {
    public function createOrder(array $data): Order {
        return DB::transaction(function () use ($data) {
            // Find or create customer
            $customer = Customer::firstOrCreate(
                ['email' => $data['customer_email']],
                ['name' => $data['customer_name'] ?? 'Guest Customer']
            );

            $subtotal = 0;
            $taxTotal = 0;
            $orderItemsData = [];

            foreach ($data['items'] as $item) {
                // Lock row for update to handle race conditions safely
                $product = Product::where('id', $item['product_id'])->lockForUpdate()->first();

                if (!$product) {
                    throw new Exception("Product not found: {$item['product_id']}");
                }

                if ($product->stock_on_hand < $item['quantity']) {
                    throw new Exception("Insufficient stock for product: {$product->name}. Available: {$product->stock_on_hand}, Requested: {$item['quantity']}");
                }

                // Deduct stock safely
                $product->stock_on_hand -= $item['quantity'];
                $product->save();

                $lineSubtotal = $product->price_per_unit * $item['quantity'];
                $lineTax = $lineSubtotal * ($product->tax_percentage / 100);
                $lineTotal = $lineSubtotal + $lineTax;

                $subtotal += $lineSubtotal;
                $taxTotal += $lineTax;

                $orderItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price_per_unit,
                    'tax_percentage' => $product->tax_percentage,
                    'line_total' => $lineTotal,
                ];
            }

            $grandTotal = $subtotal + $taxTotal;

            $order = Order::create([
                'customer_id' => $customer->id,
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'grand_total' => $grandTotal,
            ]);

            foreach ($orderItemsData as & $itemData) {
                $itemData['order_id'] = $order->id;
                $itemData['created_at'] = now();
                $itemData['updated_at'] = now();
            }

            \App\Models\OrderItem::insert($orderItemsData);

            // Dispatch queued job for order confirmation email
            SendOrderConfirmationEmail::dispatch($order->load('customer', 'items.product'));

            return $order->load('customer', 'items.product');
        });
    }
}
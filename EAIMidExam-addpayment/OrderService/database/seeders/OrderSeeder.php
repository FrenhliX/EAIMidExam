<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Create orders
        $orders = [
            [
                'user_id' => 2, // Customer 1
                'status' => 'completed',
                'total_amount' => 23000000,
                'shipping_address' => 'Jl. Merdeka No. 123, Jakarta',
                'payment_method' => 'credit_card',
                'payment_status' => 'paid'
            ],
            [
                'user_id' => 3, // Customer 2
                'status' => 'processing',
                'total_amount' => 5000000,
                'shipping_address' => 'Jl. Sudirman No. 456, Bandung',
                'payment_method' => 'bank_transfer',
                'payment_status' => 'pending'
            ]
        ];

        foreach ($orders as $order) {
            $createdOrder = Order::create($order);

            // Create order items
            if ($createdOrder->id === 1) {
                // Order 1 items
                OrderItem::create([
                    'order_id' => $createdOrder->id,
                    'product_id' => 1, // Laptop Gaming
                    'quantity' => 1,
                    'price' => 15000000
                ]);
                OrderItem::create([
                    'order_id' => $createdOrder->id,
                    'product_id' => 3, // Headphone Wireless
                    'quantity' => 1,
                    'price' => 2000000
                ]);
            } else {
                // Order 2 items
                OrderItem::create([
                    'order_id' => $createdOrder->id,
                    'product_id' => 4, // Smart Watch
                    'quantity' => 1,
                    'price' => 3000000
                ]);
                OrderItem::create([
                    'order_id' => $createdOrder->id,
                    'product_id' => 3, // Headphone Wireless
                    'quantity' => 1,
                    'price' => 2000000
                ]);
            }
        }
    }
} 
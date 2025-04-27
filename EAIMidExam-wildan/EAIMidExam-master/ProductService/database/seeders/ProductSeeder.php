<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop Gaming',
                'description' => 'Laptop gaming dengan performa tinggi',
                'price' => 15000000,
                'stock' => 10,
                'category' => 'Electronics',
                'image_url' => 'https://example.com/laptop.jpg'
            ],
            [
                'name' => 'Smartphone',
                'description' => 'Smartphone dengan kamera 48MP',
                'price' => 8000000,
                'stock' => 20,
                'category' => 'Electronics',
                'image_url' => 'https://example.com/smartphone.jpg'
            ],
            [
                'name' => 'Headphone Wireless',
                'description' => 'Headphone wireless dengan noise cancellation',
                'price' => 2000000,
                'stock' => 15,
                'category' => 'Accessories',
                'image_url' => 'https://example.com/headphone.jpg'
            ],
            [
                'name' => 'Smart Watch',
                'description' => 'Smart watch dengan fitur kesehatan',
                'price' => 3000000,
                'stock' => 8,
                'category' => 'Accessories',
                'image_url' => 'https://example.com/smartwatch.jpg'
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
} 
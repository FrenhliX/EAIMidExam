<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'address' => 'Jl. Admin No. 1'
            ],
            [
                'name' => 'Customer 1',
                'email' => 'customer1@example.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone' => '081234567891',
                'address' => 'Jl. Customer No. 1'
            ],
            [
                'name' => 'Customer 2',
                'email' => 'customer2@example.com',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone' => '081234567892',
                'address' => 'Jl. Customer No. 2'
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
} 
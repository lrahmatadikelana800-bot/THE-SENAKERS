<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@sneakers.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'address' => 'Jl. Admin No. 1, Jakarta',
        ]);

        // Create Regular User
        User::create([
            'name' => 'John Doe',
            'email' => 'john@sneakers.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'phone' => '081234567891',
            'address' => 'Jl. Customer No. 1, Bandung',
        ]);

        // Create Sample Products
        $products = [
            [
                'name' => 'Nike Air Max 270',
                'description' => 'Running shoes with Air cushioning',
                'price' => 1500000,
                'stock' => 50,
                'brand' => 'Nike',
                'size' => '42',
                'color' => 'Black/White',
                'status' => 'active',
            ],
            [
                'name' => 'Adidas Ultraboost 22',
                'description' => 'Comfortable running shoes',
                'price' => 1800000,
                'stock' => 30,
                'brand' => 'Adidas',
                'size' => '43',
                'color' => 'Blue/White',
                'status' => 'active',
            ],
            [
                'name' => 'Converse Chuck Taylor',
                'description' => 'Classic canvas shoes',
                'price' => 800000,
                'stock' => 100,
                'brand' => 'Converse',
                'size' => '41',
                'color' => 'Red',
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
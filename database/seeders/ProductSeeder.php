<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::create([
            'name' => 'Nike Air Max 270',
            'description' => 'Running shoes with max air cushion',
            'price' => 159.99,
            'stock' => 50,
            'category' => 'Running',
            'brand' => 'Nike'
        ]);

        Product::create([
            'name' => 'Adidas Ultraboost 22',
            'description' => 'Comfortable daily sneakers',
            'price' => 179.99,
            'stock' => 30,
            'category' => 'Casual',
            'brand' => 'Adidas'
        ]);

        Product::create([
            'name' => 'Converse Chuck Taylor',
            'description' => 'Classic canvas shoes',
            'price' => 65.00,
            'stock' => 100,
            'category' => 'Casual',
            'brand' => 'Converse'
        ]);
    }
}

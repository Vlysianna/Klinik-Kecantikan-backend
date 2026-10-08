<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Admin User
        User::updateOrCreate(
            ['email' => 'admin@drnich.com'],
            [
                'name' => 'Admin Dr Nich',
                'password' => Hash::make('password123'),
            ]
        );

        // Sample Clinic Products
        $products = [
            [
                'name' => 'Acne Clarifying Serum 30ml',
                'category' => 'Skincare',
                'price' => 125000.00,
                'stock' => 50,
            ],
            [
                'name' => 'Brightening Day Cream SPF 50',
                'category' => 'Skincare',
                'price' => 95000.00,
                'stock' => 40,
            ],
            [
                'name' => 'Hydrating Night Repair Cream',
                'category' => 'Skincare',
                'price' => 110000.00,
                'stock' => 35,
            ],
            [
                'name' => 'Glowing Facial Treatment',
                'category' => 'Treatment',
                'price' => 250000.00,
                'stock' => 20,
            ],
            [
                'name' => 'Deep Laser Rejuvenation Treatment',
                'category' => 'Treatment',
                'price' => 550000.00,
                'stock' => 15,
            ],
            [
                'name' => 'Chemical Peeling Treatment',
                'category' => 'Treatment',
                'price' => 300000.00,
                'stock' => 25,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                $product
            );
        }
    }
}

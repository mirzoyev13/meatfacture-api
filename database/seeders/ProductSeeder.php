<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::insert([
            [
                'name'        => 'Steak',
                'description' => 'Rare, medium, well done',
                'price'       => 22.50,
                'category'    => 'Beef',
                'in_stock'    => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Chicken',
                'description' => 'Grill, nuggets',
                'price'       => 9.90,
                'category'    => 'Chicken',
                'in_stock'    => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Pork',
                'description' => 'BBQ',
                'price'       => 13.70,
                'category'    => 'Pork',
                'in_stock'    => false,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}

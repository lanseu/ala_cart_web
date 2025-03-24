<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;
use Lunar\Models\Product;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::count() === 0) {
            Product::factory()->count(5)->create();
        }
    
        Product::all()->each(function ($product) {
            Review::factory()->count(rand(1, 3))->create([ 
                'product_id' => $product->id
            ]);
        });
    }
}
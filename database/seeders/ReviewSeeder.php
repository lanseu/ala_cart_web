<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('reviews')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::statement('ALTER TABLE reviews AUTO_INCREMENT = 1;');

        // Ensure there are at least some products
        if (Product::count() === 0) {
            Product::factory(5)->create();
        }

        $userIds = User::limit(10)->pluck('id')->toArray();
        $products = Product::all();

        if ($products->isEmpty() || empty($userIds)) {
            return;
        }

        foreach ($products as $product) {
            // Ensure each product has at least one review
            $user = User::find($userIds[array_rand($userIds)]);

            if (!Review::where('user_id', $user->id)->where('product_id', $product->id)->exists()) {
                $review = Review::factory()->create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                ]);

                $imageFile = $product->image ?? 'default.jpg';
                $imagePath = base_path("database/seeders/data/images/{$imageFile}");

                if (file_exists($imagePath)) {
                    $review->addMedia($imagePath)
                        ->preservingOriginal()
                        ->toMediaCollection('images');
                }
            }

            // Optionally add extra reviews per product
            foreach (range(1, rand(1, 3)) as $_) {
                $extraUser = User::find($userIds[array_rand($userIds)]);
                if (!Review::where('user_id', $extraUser->id)->where('product_id', $product->id)->exists()) {
                    Review::factory()->create([
                        'product_id' => $product->id,
                        'user_id' => $extraUser->id,
                    ]);
                }
            }
        }
    }
}

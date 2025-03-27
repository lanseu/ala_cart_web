<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Lunar\Models\Product;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'product_id' => Product::inRandomOrder()->first()->id ?? Product::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'review' => $this->faker->realText(100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

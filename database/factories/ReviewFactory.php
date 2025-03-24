<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Lunar\Models\Product; 
use Faker\Factory as FakerFactory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        $faker = FakerFactory::create('en_US');

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'product_id' => Product::inRandomOrder()->first()?->id ?? Product::factory(), 
            'rating' => $this->faker->numberBetween(1, 5),
            'review' => $faker->realText(100),
            'images' => json_encode([
                $this->faker->imageUrl(200, 200, 'fashion'),
                $this->faker->imageUrl(200, 200, 'fashion'),
            ]),
        ];
    }
}

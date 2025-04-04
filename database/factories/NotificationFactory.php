<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id'   => User::inRandomOrder()->first()->id ?? User::factory(),
            'title'     => $this->faker->sentence(3),
            'body'      => $this->faker->sentence(10),
            'type'      => $this->faker->randomElement(['message', 'alert', 'sale']),
            'status'    => $this->faker->randomElement(['read', 'unread']),
            'image_url' => $this->faker->imageUrl(100, 100, 'business', true, 'notification'),
            'created_at'=> now(),
        ];
    }
}

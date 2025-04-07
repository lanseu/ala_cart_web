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
        $name = $this->faker->randomElement([
            'Nike', 'Levis', 'Converse', 'Saltrock', 'NICCE', 'Fruit of the Loom',
        ]);

        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'sender_name' => $name,
            'title' => $this->faker->sentence(3),
            'body' => $this->faker->sentence(10),
            'type' => $this->faker->randomElement(['message', 'alert', 'sale']),
            'status' => $this->faker->randomElement(['read', 'unread']),
            'created_at' => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Notification $notification) {
            $iconFileName = strtolower(str_replace(' ', '_', $notification->sender_name)).'.jpg';
            $iconPath = storage_path("app/public/icons/{$iconFileName}");

            if (file_exists($iconPath)) {
                $notification->addMedia($iconPath)
                    ->preservingOriginal()
                    ->toMediaCollection('icon');
            }
        });
    }
}

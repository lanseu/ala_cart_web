<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'Nike', 'Levis', 'Converse', 'Saltrock', 'NICCE', 'Fruit of the Loom',
        ]);

        return [
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory(),
            'type' => $this->faker->randomElement(['conversation', 'promotion']),
            'name' => $name,
            'chat' => $this->faker->sentence(),
            'timestamp' => now(),
            'hasUnread' => $this->faker->boolean(),
            'isMe' => $this->faker->boolean(),
        ];
    }

    /**
     * Configure the factory.
     */
    public function configure()
    {
        return $this->afterCreating(function (Message $message) {
            $iconFileName = strtolower(str_replace(' ', '_', $message->name)).'.jpg'; // Match file naming convention
            $iconPath = storage_path("app/public/icons/{$iconFileName}");

            if (file_exists($iconPath)) {
                $message->addMedia($iconPath)
                    ->preservingOriginal()
                    ->toMediaCollection('icon');
            }
        });
    }
}

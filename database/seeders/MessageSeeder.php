<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MessageSeeder extends Seeder
{
    public function run()
    {
        // Ensure there are users in the database
        if (User::count() === 0) {
            User::factory()->count(10)->create(); // Create 10 random users if none exist
        }

        $messages = [
            [
                'type' => 'conversation',
                'name' => 'Nike',
                'chat' => 'Hello, this is a test conversation!',
                'hasUnread' => true,
                'isMe' => true,
                'icon_file' => 'nike.jpg',
            ],
            [
                'type' => 'promotion',
                'name' => 'Levis',
                'chat' => 'Check out our latest promo!',
                'hasUnread' => false,
                'isMe' => false,
                'icon_file' => 'levis.jpg',
            ],
            [
                'type' => 'conversation',
                'name' => 'Converse',
                'chat' => 'Limited stock available!',
                'hasUnread' => true,
                'isMe' => false,
                'icon_file' => 'converse.jpg',
            ],
            [
                'type' => 'promotion',
                'name' => 'Saltrock',
                'chat' => 'New arrivals just dropped!',
                'hasUnread' => false,
                'isMe' => true,
                'icon_file' => 'saltrock.jpg',
            ],
            [
                'type' => 'conversation',
                'name' => 'NICCE',
                'chat' => 'Your order has been shipped!',
                'hasUnread' => true,
                'isMe' => false,
                'icon_file' => 'nicce.jpg',
            ],
            [
                'type' => 'promotion',
                'name' => 'Fruit of the Loom',
                'chat' => 'Exclusive discounts available now!',
                'hasUnread' => false,
                'isMe' => false,
                'icon_file' => 'fruit_of_the_loom.jpg',
            ],
        ];

        foreach ($messages as $data) {
            $randomUser = User::inRandomOrder()->first(); // Get a random user
            $data['user_id'] = $randomUser->id; // Assign the message to the user

            // Create Message
            $message = Message::create([
                'user_id' => $data['user_id'],
                'type' => $data['type'],
                'name' => $data['name'],
                'chat' => $data['chat'],
                'timestamp' => now(),
                'hasUnread' => $data['hasUnread'],
                'isMe' => $data['isMe'],
            ]);

            // Attach icon using Spatie Media Library
            $iconPath = storage_path("app/public/icons/{$data['icon_file']}");

            if (file_exists($iconPath)) {
                $message->addMedia($iconPath)
                        ->preservingOriginal()
                        ->toMediaCollection('icon');
            }
        }
    }
}

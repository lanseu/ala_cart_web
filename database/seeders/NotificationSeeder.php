<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Ensure there are users in the database
        if (User::count() === 0) {
            User::factory()->count(10)->create(); // Create 10 random users if none exist
        }

        $notifications = [
            [
                'sender_name' => 'Nike',
                'title' => 'New Collection Drop!',
                'body' => 'Check out the latest styles from Nike.',
                'type' => 'promotion',
                'status' => 'unread',
                'icon_file' => 'nike.jpg',
            ],
            [
                'sender_name' => 'Levis',
                'title' => 'Exclusive Offer!',
                'body' => 'Enjoy 20% off on all jeans this weekend.',
                'type' => 'promotion',
                'status' => 'read',
                'icon_file' => 'levis.jpg',
            ],
            [
                'sender_name' => 'Converse',
                'title' => 'Limited Edition Alert!',
                'body' => 'Get your hands on our limited-edition sneakers.',
                'type' => 'alert',
                'status' => 'unread',
                'icon_file' => 'converse.jpg',
            ],
            [
                'sender_name' => 'Saltrock',
                'title' => 'Special Discount for You!',
                'body' => 'Save 15% on your next Saltrock purchase.',
                'type' => 'promotion',
                'status' => 'read',
                'icon_file' => 'saltrock.jpg',
            ],
            [
                'sender_name' => 'NICCE',
                'title' => 'Shipping Update!',
                'body' => 'Your recent order has been shipped.',
                'type' => 'message',
                'status' => 'unread',
                'icon_file' => 'nicce.jpg',
            ],
            [
                'sender_name' => 'Fruit of the Loom',
                'title' => 'Flash Sale Now Live!',
                'body' => 'Hurry! Limited-time discounts on all items.',
                'type' => 'promotion',
                'status' => 'unread',
                'icon_file' => 'fruit_of_the_loom.jpg',
            ],
        ];

        foreach ($notifications as $data) {
            $randomUser = User::inRandomOrder()->first(); // Get a random user
            $data['user_id'] = $randomUser->id; // Assign the notification to the user

            // Create Notification
            $notification = Notification::create([
                'user_id' => $data['user_id'],
                'sender_name' => $data['sender_name'],
                'title' => $data['title'],
                'body' => $data['body'],
                'type' => $data['type'],
                'status' => $data['status'],
                'created_at' => now(),
            ]);

            // Attach icon using Spatie Media Library
            $iconPath = storage_path("app/public/icons/{$data['icon_file']}");

            if (file_exists($iconPath)) {
                $notification->addMedia($iconPath)
                    ->preservingOriginal()
                    ->toMediaCollection('icon');
            }
        }
    }
}

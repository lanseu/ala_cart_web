<?php

namespace App\Services;

use App\Models\Message;

class MessageService
{
    // Get all messages
    public function getAllMessages()
    {
        return Message::with(['user'])
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'user_id' => $message->user_id,
                    'name' => $message->name,
                    'type' => $message->type,
                    'chat' => $message->chat,
                    'iconpath' => $message->getFirstMediaUrl('icon'),
                    'timestamp' => $message->timestamp,
                    'hasUnread' => (bool) $message->hasUnread,
                    'isMe' => (bool) $message->isMe,
                    'created_at' => $message->created_at,
                    'updated_at' => $message->updated_at,
                ];
            });
    }

    // Get messages by userId
    public function getMessagesByUserId($userId)
    {
        $messages = Message::where('user_id', $userId)
            ->whereNull('parent_id')  // Get only store messages with no parent_id
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'chat' => $message->chat,
                    'timestamp' => $message->created_at,
                    'isMe' => $message->isMe,
                    'iconpath' => $message->getFirstMediaUrl('icon') ?: asset('default-icon.png'),
                    'name' => $message->name,
                    'type' => $message->type,
                    'hasUnread' => $message->hasUnread,
                ];
            });

        return response()->json($messages);
    }

    // Get message by ID
    public function getMessageById($id)
    {
        return Message::with(['user'])->findOrFail($id);
    }

    // Create a new message
    public function createMessage(array $data)
    {
        $message = Message::create($data);

        if (isset($data['icon'])) {
            $message->addMedia($data['icon'])->toMediaCollection('icon');
        }

        return $message;
    }

    // Update a message
    public function updateMessage($id, array $data)
    {
        $message = Message::findOrFail($id);
        $message->update($data);

        return $message;
    }

    // Delete a message
    public function deleteMessage($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();
    }

    public function getReplies($id)
    {
        return Message::where('parent_id', $id)->get();
    }

    public function replyToMessage($messageId, $user, $fullName, $chatContent)
    {
        if (! $user) {
            throw new \Exception('User not authenticated');
        }

        \Log::info('Fetching original message with ID: '.json_encode($messageId));

        $originalMessage = Message::findOrFail($messageId);

        if (! $originalMessage) {
            \Log::error('❌ Original message not found!');
            throw new \Exception('Original message not found');
        }

        $profilePicture = $user->getFirstMediaUrl('profile_picture') ?: null;
        \Log::info('Creating reply with parent_id: '.$originalMessage->id);

        $reply = Message::create([
            'user_id' => $user->id,
            'name' => $fullName,
            'type' => 'conversation',
            'chat' => $chatContent,
            'parent_id' => $originalMessage->id,
            'timestamp' => now(),
            'hasUnread' => true,
            'isMe' => 1,
        ]);

        if ($profilePicture) {
            $reply->iconpath = $profilePicture;
            $reply->save();
        }

        return $reply;
    }
}

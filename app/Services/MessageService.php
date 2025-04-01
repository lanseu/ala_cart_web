<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Reply;

class MessageService
{
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

    public function getMessagesByUserId($userId)
    {
        return Message::where('user_id', $userId)
            ->orderBy('timestamp', 'desc')
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

    public function getMessageById($id)
    {
        return Message::with(['user'])->findOrFail($id);
    }

    public function createMessage(array $data)
    {
        $message = Message::create($data);

        if (isset($data['icon'])) {
            $message->addMedia($data['icon'])->toMediaCollection('icon');
        }

        return $message;
    }

    public function updateMessage($id, array $data)
    {
        $message = Message::findOrFail($id);
        $message->update($data);

        return $message;
    }

    public function deleteMessage($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();
    }

    public function getReplies($id)
    {
        return Message::where('parent_id', $id)->get();
    }

    public function replyToMessage($data)
    {
        return Reply::create($data);
    }
}

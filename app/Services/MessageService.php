<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageService
{
    public function getAllMessages()
    {
        return Message::with(['user'])->get();
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

    public function replyToMessage($parentId, array $data)
    {
        $data['parent_id'] = $parentId;
        return Message::create($data);
    }
}

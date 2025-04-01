<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $fillable = [
        'message_id',
        'user_id',
        'chat',
        'timestamp',
        'hasUnread',
        'isMe',
    ];

    // Replies belong to a message
    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}

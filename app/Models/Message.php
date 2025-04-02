<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Message extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'chat',
        'timestamp',
        'hasUnread',
        'isMe',
        'parent_id', 
    ];

    // ✅ A message belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ✅ A message may have multiple replies (self-referencing relationship)
    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id');
    }

    // ✅ A message may belong to a parent message (if it's a reply)
    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('icon')->singleFile();
    }

    // ✅ Accessor to get the icon path
    public function getIconPathAttribute()
    {
        return $this->getFirstMediaUrl('icon') ?: null;
    }
}

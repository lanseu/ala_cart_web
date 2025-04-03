<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Lunar\Models\CartLine;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'currency_id',
        'channel_id',
        'order_id',
        'coupon_code',
        'completed_at',
        'meta',
    ];

    // Relationships
    public function cartLines()
    {
        return $this->hasMany(CartLine::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

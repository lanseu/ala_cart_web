<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Order extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'lunar_orders';

    protected $fillable = [
        'user_id',
        'customer_id',
        'status',
        'sub_total',
        'discount_total',
        'tax_total',
        'shipping_total',
        'total',
        'currency_code',
        'customer_reference',
        'notes',
        'channel_id',
        'tax_breakdown',
    ];

    public function lines()
    {
        return $this->hasMany(OrderLine::class, 'order_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product')->singleFile();
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('product');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;

class OrderLine extends Model
{
    use InteractsWithMedia;

    protected $table = 'lunar_order_lines';

    protected $fillable = [
        'order_id',
        'purchasable_type',
        'purchasable_id',
        'type',
        'description',
        'option',
        'identifier',
        'unit_price',
        'unit_quantity',
        'quantity',
        'sub_total',
        'discount_total',
        'tax_breakdown',
        'tax_total',
        'total',
        'notes',
        'meta',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'purchasable_id');
    }

    public function getImageUrlAttribute()
    {
        return $this->product
            ? $this->product->getFirstMediaUrl('product_images')
            : null;
    }
}

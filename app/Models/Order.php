<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'lunar_orders';

    protected $fillable = [
        'user_id',
        'status',
        'sub_total',
        'discount_total',
        'tax_total',
        'shipping_total',
        'total',
        'currency_code',
        'customer_reference',
        'notes'
    ];

    public function lines()
    {
        return $this->hasMany(OrderLine::class, 'order_id');
    }
}

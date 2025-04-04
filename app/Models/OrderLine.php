<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLine extends Model
{
    protected $table = 'lunar_order_lines';

    protected $fillable = [
        'order_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'line_total',
        'tax_total'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}

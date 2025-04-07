<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{

    protected $table = 'lunar_order_addresses';

    // Fillable fields for mass assignment.
    protected $fillable = [
        'order_id',
        'cart_id',
        'country_id',
        'title',
        'first_name',
        'last_name',
        'company_name',
        'line_one',
        'line_two',
        'line_three',
        'city',
        'state',
        'postcode',
        'delivery_instructions',
        'contact_email',
        'contact_phone',
        'type',
        'shipping_option',
        'meta',
    ];

    // Optionally, cast meta to an array if stored as JSON.
    protected $casts = [
        'meta' => 'array',
    ];
}

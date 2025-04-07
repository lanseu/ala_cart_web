<?php

namespace App\Models;

use Lunar\Models\Address as LunarCartAddress;

class Address extends LunarCartAddress
{
    protected $fillable = [
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
        'meta'
    ];

    // You can define any custom methods or relationships here
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Example custom method to get full address
    public function getFullAddressAttribute()
    {
        return $this->line_one . ' ' . $this->line_two . ' ' . $this->line_three . ', ' . $this->city . ', ' . $this->state . ' ' . $this->postcode;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOptionValue extends Model
{
    protected $fillable = ['value'];

    public function variants()
    {
        return $this->belongsToMany(ProductVariant::class, 'lunar_product_option_value_product_variant', 'value_id', 'variant_id');
    }
}

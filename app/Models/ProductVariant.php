<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Lunar\Models\Price;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductVariant as LunarProductVariant;

class ProductVariant extends LunarProductVariant
{
    protected $fillable = ['product_id', 'sku', 'stock', 'purchasable'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(\Lunar\Models\Product::class);
    }      
    
    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductOptionValue::class,
            'lunar_product_option_value_product_variant',
            'variant_id',
            'value_id'
        );
    }

    public function price()
    {
        return $this->hasOne(Price::class, 'priceable_id')->where('priceable_type', 'App\\Models\\Variant');
    }
}

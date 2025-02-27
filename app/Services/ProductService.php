<?php

namespace App\Services;

use Lunar\Models\Product;

class ProductService
{
    public function getAllProducts()
    {
        return Product::with(['images', 'prices'])
        ->paginate()
    ->through(function ($product) {
        foreach ($product->images as $image) {
            $image->url = url($image->path);
        }

        $product->price = $product->prices->first()?->price->value ?? null;

        // ✅ Use variant-level stock if available
        $product->stock = $product->prices->first()?->priceable?->stock ?? 0;

        return $product;
    });
        
    }
    public function createProduct(array $data)
    {
        return Product::create($data);
    }

    public function getProductById($id)
    {
        return Product::find($id);
    }

    public function updateProduct($id, array $data)
    {
        $product = Product::find($id);
        if (! $product) {
            return null;
        }

        $product->update($data);

        return $product;
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);
        if (! $product) {
            return false;
        }

        $product->delete();

        return true;
    }
}

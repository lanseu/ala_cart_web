<?php

namespace App\Services;

use Log;
use Lunar\Models\Cart;  // Use Lunar's Cart model
use Lunar\Models\CartLine;
use Lunar\Models\ProductVariant;

class CartService
{
    public function getUserCart($userId)
    {
        $cart = Cart::where('user_id', $userId)
            ->with('lines.purchasable.prices') // Load purchasable and its prices
            ->first();

        if (! $cart) {
            return ['message' => 'Cart not found'];
        }

        return [
            'cart_id' => $cart->id,
            'items' => $cart->lines->map(function ($line) {
                $variant = $line->purchasable; // Use 'purchasable' for polymorphic relation

                if (! $variant) {
                    return [
                        'id' => $line->id,
                        'quantity' => $line->quantity,
                        'total' => 0,
                        'purchasable' => null, // Handle missing variant gracefully
                    ];
                }

                return [
                    'id' => $line->id,
                    'quantity' => $line->quantity,
                    'total' => $line->price ?? (($variant->prices->first()->price->value ?? 0) * $line->quantity),
                    'purchasable' => [
                        'id' => $variant->id,
                        'sku' => $variant->sku,
                        'price' => optional($variant->prices->first())->price ?? null,
                        'stock' => $variant->stock,
                        'product_name' => optional($variant->product)->translateAttribute('name') ?? 'Unknown Product',
                        'image' => optional($variant->getThumbnail())->getUrl(),
                    ],
                ];
            }),
        ];
    }

    public function addItemToCart($cart, $productId, $variantId, $quantity)
    {
        Log::info("Add to Cart Request: Product ID: $productId, Variant ID: $variantId");

        // Check for product variant
        $productVariant = ProductVariant::where('id', $variantId)
            ->where('product_id', $productId)
            ->first();

        if (! $productVariant) {
            Log::error("Invalid product variant: Variant ID: $variantId does not belong to Product ID: $productId");

            return ['error' => 'Invalid product variant selected.'];
        }

        // Check stock
        if ($productVariant->stock < $quantity) {
            return ['error' => 'Not enough stock available.'];
        }

        // Update or create CartLine for this item in the existing cart
        $cartLine = CartLine::updateOrCreate(
            [
                'cart_id' => $cart->id,
                'purchasable_id' => $variantId,
                'purchasable_type' => 'Lunar\Models\ProductVariant',
            ],
            ['quantity' => $quantity]
        );

        // Decrement the stock of the product variant
        $productVariant->decrement('stock', $quantity);

        return [
            'message' => 'Item added to cart successfully!',
            'cart_line' => $cartLine,
            'remaining_stock' => $productVariant->stock,
        ];
    }

    public function updateCartItem($userId, $cartLineId, $newQuantity)
    {
        $cartLine = CartLine::whereHas('cart', fn ($query) => $query->where('user_id', $userId))
            ->findOrFail($cartLineId);

        $productVariant = ProductVariant::find($cartLine->purchasable_id);

        if (! $productVariant) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $stockDifference = $newQuantity - $cartLine->quantity;

        if ($stockDifference > 0 && $productVariant->stock < $stockDifference) {
            return response()->json(['error' => 'Not enough stock available'], 400);
        }

        if ($stockDifference > 0) {
            $productVariant->decrement('stock', $stockDifference);
        } else {
            $productVariant->increment('stock', abs($stockDifference));
        }

        $cartLine->update(['quantity' => $newQuantity]);

        return ['success' => true, 'stock' => $productVariant->stock];
    }

    public function deleteCartItem($userId, $cartLineId)
    {
        try {
            \Log::info("Attempting to delete CartLine ID: $cartLineId for User: $userId");

            $cartLine = CartLine::whereHas('cart', fn ($query) => $query->where('user_id', $userId))
                ->find($cartLineId);

            if (! $cartLine) {
                \Log::error("CartLine not found with ID: $cartLineId for User: $userId");

                return ['error' => 'Cart item not found', 'status' => 404];
            }

            $productVariant = ProductVariant::find($cartLine->purchasable_id);

            if ($productVariant) {
                \Log::info("Restoring stock for Variant ID: {$productVariant->id}, Quantity: {$cartLine->quantity}");
                $productVariant->increment('stock', $cartLine->quantity);
            } else {
                \Log::warning("Product Variant not found for CartLine ID: $cartLineId");
            }

            $cart = $cartLine->cart;
            $cartLine->delete();
            if ($cart && $cart->lines()->count() == 0) {
                \Log::info("Cart with ID: {$cart->id} is empty, deleting the cart.");
                $cart->delete();
            }

            return ['success' => true, 'status' => 200];

        } catch (\Exception $e) {
            \Log::error('Error deleting cart item: '.$e->getMessage());

            return ['error' => 'An error occurred while deleting the cart item', 'status' => 500];
        }
    }

    public function getCartItemCount($userId)
    {
        $cart = Cart::where('user_id', $userId)->first();

        if (! $cart) {
            return 0;
        }

        return $cart->lines->sum('quantity');
    }
}

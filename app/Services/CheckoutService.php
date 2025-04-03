<?php

namespace App\Services;

use Lunar\Models\Order;
use Lunar\Models\Cart;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function createOrder(Cart $cart, $userId, $notes)
    {
        $totalAmount = $this->cartService->calculateTotalAmount($cart);

        DB::beginTransaction();

        try {
            $order = Order::create([
                'customer_id' => $cart->customer_id,
                'user_id' => $userId,
                'channel_id' => $cart->channel_id,
                'new_customer' => $cart->new_customer,
                'status' => 'pending',
                'reference' => uniqid('order_'),
                'customer_reference' => null,
                'sub_total' => $totalAmount['sub_total'],
                'discount_total' => $totalAmount['discount_total'],
                'discount_breakdown' => json_encode($totalAmount['discounts']),
                'shipping_breakdown' => json_encode($totalAmount['shipping']),
                'shipping_total' => $totalAmount['shipping_total'],
                'tax_breakdown' => json_encode($totalAmount['taxes']),
                'tax_total' => $totalAmount['tax_total'],
                'total' => $totalAmount['total'],
                'notes' => $notes ?? null,
                'currency_code' => $cart->currency_code,
                'compare_currency_code' => $cart->compare_currency_code,
                'exchange_rate' => 1,
                'placed_at' => now(),
                'meta' => json_encode(['payment_method' => 'cash-on-delivery']),
            ]);

            DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Order creation failed: ' . $e->getMessage());
        }
    }
}

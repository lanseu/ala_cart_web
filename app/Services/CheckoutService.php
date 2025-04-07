<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderAddress;
use Illuminate\Support\Facades\Log;
use Lunar\Models\Currency;
use Lunar\Models\Price;
use App\Models\ProductVariant;

class CheckoutService
{
    public function calculateTotalAmount($cart)
    {
        $subTotal = 0;
        $discounts = [];
        $taxTotal = 0;

        $currency = Currency::where('default', 1)->first();
        if (!$currency) {
            throw new \Exception('No default currency found');
        }

        foreach ($cart->lines as $line) {
            // Fallback logic for unit price
            $unitPrice = $line->unit_price->value ?? null;

            if (!$unitPrice) {
                $variant = ProductVariant::find($line->purchasable_id);

                $price = Price::where('priceable_type', 'product_variant')
                    ->where('priceable_id', $variant->id)
                    ->where('currency_id', $currency->id)
                    ->first();

                if ($price && $price->price) {
                    $unitPrice = $price->price->value;
                } else {
                    \Log::warning("Price not found for variant ID {$line->purchasable_id}");
                    $unitPrice = 0;
                }
            }

            $quantity = $line->quantity;
            $subTotalLine = $unitPrice * $quantity;
            $subTotal += $subTotalLine;

            // Handle discount
            if ($line->discount) {
                $discounts[] = $line->discount;
            }

            // Calculate tax for this line
            $taxPercentage = 20;
            $taxValue = round($subTotalLine * ($taxPercentage / 100), 2);
            $taxTotal += $taxValue;
        }

        // Sum discounts
        $discountTotal = array_sum(array_column($discounts, 'amount'));

        // Shipping logic
        $shippingTotal = $this->calculateShipping($subTotal);

        // Final total
        $total = $subTotal - $discountTotal + $shippingTotal + $taxTotal;

        return [
            'sub_total'      => $subTotal,
            'discount_total' => $discountTotal,
            'discounts'      => $discounts,
            'shipping_total' => $shippingTotal,
            'tax_total'      => $taxTotal,
            'total'          => $total,
        ];
    }

    private function calculateShipping($subTotal)
    {
        if ($subTotal >= 1000) {
            return 0;
        }

        return round($subTotal * 0.1);
    }

    public function updateCartAddresses($cart, array $addresses)
    {
        $cart->addresses()->delete();

        foreach ($addresses as $addressData) {
            $cart->addresses()->create([
                'country_id'            => $addressData['country_id'] ?? null,
                'title'                 => $addressData['title'] ?? null,
                'first_name'            => $addressData['first_name'] ?? null,
                'last_name'             => $addressData['last_name'] ?? null,
                'company_name'          => $addressData['company_name'] ?? null,
                'line_one'              => $addressData['line_one'] ?? null,
                'line_two'              => $addressData['line_two'] ?? null,
                'line_three'            => $addressData['line_three'] ?? null,
                'city'                  => $addressData['city'] ?? null,
                'state'                 => $addressData['state'] ?? null,
                'postcode'              => $addressData['postcode'] ?? null,
                'delivery_instructions' => $addressData['delivery_instructions'] ?? null,
                'contact_email'         => $addressData['contact_email'] ?? null,
                'contact_phone'         => $addressData['contact_phone'] ?? null,
                'type'                  => $addressData['type'] ?? null,
                'shipping_option'       => $addressData['shipping_option'] ?? null,
                'meta'                  => isset($addressData['meta']) ? json_encode($addressData['meta']) : null,
            ]);
        }

        $cart->load('addresses');
    }

    public function createOrder($cart, $userId, $totalAmount, $notes)
    {
        $taxBreakdown = [];

        $currency = Currency::where('default', 1)->first();
        if (!$currency) {
            throw new \Exception('No default currency found');
        }

        $currencyCode = $currency->code;
        $exchangeRate = $currency->exchange_rate;

        $variantIds = $cart->lines->pluck('purchasable_id');
        $variants = ProductVariant::with(['product', 'optionValues'])
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        foreach ($cart->lines as $line) {
            $taxPercentage = 20;
            $taxValue = ($line->unit_price * $line->quantity) * ($taxPercentage / 100);
            $taxBreakdown[] = [
                "description"   => "VAT",
                "identifier"    => "VAT",
                "percentage"    => $taxPercentage,
                "value"         => round($taxValue * $exchangeRate),
                "currency_code" => $currencyCode,
            ];
        }

        try {
            $order = Order::create([
                'user_id'            => $userId,
                'customer_id'        => $userId,
                'status'             => 'pending',
                'sub_total'          => $totalAmount['sub_total'],
                'discount_total'     => $totalAmount['discount_total'],
                'discount_breakdown' => json_encode($totalAmount['discounts']),
                'shipping_total'     => $totalAmount['shipping_total'],
                'tax_total'          => $totalAmount['tax_total'],
                'total'              => $totalAmount['total'],
                'currency_code'      => $currencyCode,
                'notes'              => $notes,
                'placed_at'          => now(),
                'meta'               => json_encode(['payment_method' => 'cash-on-delivery']),
                'channel_id'         => $cart->channel_id ?? 1,
                'tax_breakdown'      => json_encode($taxBreakdown),
            ]);

            if ($cart->addresses && count($cart->addresses)) {
                foreach ($cart->addresses as $cartAddress) {
                    OrderAddress::create([
                        'order_id'              => $order->id,
                        'country_id'            => $cartAddress->country_id,
                        'title'                 => $cartAddress->title,
                        'first_name'            => $cartAddress->first_name,
                        'last_name'             => $cartAddress->last_name,
                        'company_name'          => $cartAddress->company_name,
                        'line_one'              => $cartAddress->line_one,
                        'line_two'              => $cartAddress->line_two,
                        'line_three'            => $cartAddress->line_three,
                        'city'                  => $cartAddress->city,
                        'state'                 => $cartAddress->state,
                        'postcode'              => $cartAddress->postcode,
                        'delivery_instructions' => $cartAddress->delivery_instructions,
                        'contact_email'         => $cartAddress->contact_email,
                        'contact_phone'         => $cartAddress->contact_phone,
                        'type'                  => $cartAddress->type,
                        'shipping_option'       => $cartAddress->shipping_option,
                        'meta'                  => $cartAddress->meta,
                    ]);
                }
            } else {
                Log::warning('No addresses found in the cart during order creation.');
            }

            foreach ($cart->lines as $line) {
                $variant = $variants[$line->purchasable_id] ?? null;
                if (!$variant) {
                    throw new \Exception("Variant not found for ID: {$line->purchasable_id}");
                }

                $priceRecord = Price::where('priceable_type', 'product_variant')
                    ->where('priceable_id', $variant->id)
                    ->where('currency_id', $currency->id)
                    ->first();

                if (!$priceRecord || !isset($priceRecord->price->value)) {
                    throw new \Exception("No price found for variant {$variant->id}");
                }

                $unitPrice = $priceRecord->price->value;
                $quantity = $line->quantity;
                $subTotalLine = $unitPrice * $quantity;

                $taxValue = round(($subTotalLine * ($taxPercentage / 100)) * $exchangeRate, 2);
                $lineTotal = $subTotalLine + $taxValue;

                $productName = 'Unnamed Product';
                if ($variant->product) {
                    $product = $variant->product;
                    $productName = $product->translateAttribute('name');
                }

                $options = $variant->optionValues->map(function ($opt) {
                    return $opt->translate('name');
                })->toArray();

                if ($productName === 'Unnamed Product') {
                    Log::warning("Product name is 'Unnamed Product' for variant ID: {$variant->id}");
                }

                if (empty($options) || in_array('Unnamed', $options)) {
                    Log::warning("Option for variant ID {$variant->id} is missing or 'Unnamed': " . json_encode($options));
                }

                $order->lines()->create([
                    'purchasable_type' => 'product_variant',
                    'purchasable_id'   => $variant->id,
                    'type'             => 'physical',
                    'identifier'       => $variant->sku,
                    'quantity'         => $quantity,
                    'unit_quantity'    => 1,
                    'unit_price'       => $unitPrice,
                    'sub_total'        => $subTotalLine,
                    'discount_total'   => 0,
                    'tax_breakdown'    => json_encode([[ 'description' => 'VAT', 'identifier' => 'VAT', 'percentage' => $taxPercentage, 'value' => $taxValue, 'currency_code' => $currencyCode ]]),
                    'tax_total'        => $taxValue,
                    'total'            => $lineTotal,
                    'description'      => $productName,
                    'option'           => json_encode($options),
                    'notes'            => null,
                    'meta'             => null,
                ]);
            }

            return $order;
        } catch (\Exception $e) {
            Log::error('Order creation failed: ', ['message' => $e->getMessage()]);
            throw new \Exception('Order creation failed: ' . $e->getMessage());
        }
    }
}

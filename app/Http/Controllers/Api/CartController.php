<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Http\Requests\ProcessOrderRequest;
use App\Models\Customer;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lunar\Models\Cart;
use Lunar\Models\Channel;
use Lunar\Models\Currency;

class CartController extends Controller
{
    private $cartService;

    private $checkoutService;

    public function __construct(CartService $cartService, CheckoutService $checkoutService)
    {
        $this->cartService = $cartService;
        $this->checkoutService = $checkoutService;
    }

    public function getCart()
    {
        $userId = Auth::id();

        return response()->json($this->cartService->getUserCart($userId));
    }

    // Add item to cart
    public function addItem(CartRequest $request, CartService $cartService)
    {
        $validated = $request->validated();
        $userId = Auth::id();

        if (! $userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $customer = Customer::where('user_id', $userId)->first();

        if (! $customer) {
            return response()->json(['error' => 'Customer not found for this user.'], 400);
        }

        $cart = Cart::firstOrCreate(
            ['user_id' => $userId],
            [
                'customer_id' => $customer->id,
                'currency_id' => Currency::first()->id,
                'channel_id' => Channel::first()->id,
            ]
        );

        $result = $cartService->addItemToCart($cart, $validated['product_id'], $validated['variant_id'], $validated['quantity']);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }

        return response()->json([
            'message' => $result['message'],
            'cart_line' => $result['cart_line'],
            'remaining_stock' => $result['remaining_stock'],
        ]);
    }

    public function updateItem(Request $request, $cartLineId)
    {
        $userId = Auth::id();
        $request->validate(['quantity' => 'required|integer|min:1']);

        return response()->json(
            $this->cartService->updateCartItem($userId, $cartLineId, $request->quantity)
        );
    }

    public function deleteItem($cartLineId)
    {
        $userId = auth()->id();

        if (! $userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $response = $this->cartService->deleteCartItem($userId, $cartLineId);

        return response()->json($response, $response['status']);
    }

    public function getCartItemCount()
    {
        $userId = Auth::id();
        $count = $this->cartService->getCartItemCount($userId);

        return response()->json(['cart_count' => $count]);
    }

    
    public function processOrder(ProcessOrderRequest $request)
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $cart = Cart::where('user_id', $userId)->first();
        if (!$cart) {
            return response()->json(['error' => 'Cart not found'], 400);
        }
        
        if ($request->has('addresses')) {
            $this->checkoutService->updateCartAddresses($cart, $request->addresses);
        }

        try {
            $totalAmount = $this->checkoutService->calculateTotalAmount($cart);
            $order = $this->checkoutService->createOrder($cart, $userId, $totalAmount, $request->notes);

            return response()->json([
                'order_id'       => $order->id,
                'payment_method' => 'Cash on Delivery',
                'total_amount'   => $order->total,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Order creation failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

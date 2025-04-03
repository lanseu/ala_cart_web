<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Models\Product;
use App\Services\CartService;
use App\Services\CheckoutService; // Import the CheckoutService
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lunar\Models\CartLine;
use Lunar\Models\Cart;

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

    public function addItem(CartRequest $request)
    {
        $userId = Auth::id();

        return response()->json(
            $this->cartService->addItemToCart($userId, $request->product_id, $request->quantity)
        );
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

        if (!$userId) {
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

    public function checkout(Request $request)
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $cart = Cart::where('user_id', $userId)->first();

        if (!$cart) {
            return response()->json(['error' => 'Cart not found'], 400);
        }

        try {
            $order = $this->checkoutService->createOrder($cart, $userId, $request->notes);

            return response()->json([
                'order_id' => $order->id,
                'payment_method' => 'Cash on Delivery',
                'total_amount' => $order->total,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Order creation failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

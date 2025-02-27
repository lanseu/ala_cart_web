<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
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
        $userId = Auth::id();

        return response()->json(['success' => $this->cartService->deleteCartItem($userId, $cartLineId)]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(): JsonResponse
    {
        $orders = $this->orderService->getAll();
        return response()->json(['orders' => $orders]);
    }

    public function show($id): JsonResponse
    {
        $order = $this->orderService->getById($id);
        return response()->json(['order' => $order]);
    }

    public function store(OrderRequest $request): JsonResponse
    {
        $order = $this->orderService->store($request->validated());
        return response()->json(['order' => $order], 201);
    }
    public function destroy($id): JsonResponse
    {
        $this->orderService->delete($id);
        return response()->json(['message' => 'Order deleted successfully.']);
    }
    public function getByUserId($userId): JsonResponse
{
    $orders = $this->orderService->getByUserId($userId);

    $ordersWithMedia = $orders->map(function ($order) {
        $firstLine = $order->lines->first();

        return [
            'id' => $order->id,
            'total' => $order->total,
            'created_at' => $order->created_at->toDateTimeString(),
            'customer_reference' => $order->customer_reference ?? 'N/A',
            'items_count' => $order->lines->count(),
            'image_url' => $firstLine && $firstLine->product
                ? $firstLine->product->getFirstMediaUrl('images')
                : 'https://via.placeholder.com/60', 
        ];
    });

    return response()->json(['orders' => $ordersWithMedia]);
}

}

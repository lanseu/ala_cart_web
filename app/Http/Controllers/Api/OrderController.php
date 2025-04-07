<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\ProductVariant;
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
        $orders = $this->orderService->getByUserId($userId)
            ->sortBy('id')
            ->values();

        $ordersWithMedia = $orders->map(function ($order) {
            $firstLine = $order->lines->first();
            $imageUrl = null;
            $description = null;
            $options = [];

            if ($firstLine) {
                $description = $firstLine->description;
                $options = json_decode($firstLine->option, true) ?? [];

                $variant = ProductVariant::find($firstLine->purchasable_id);

                if ($variant && $variant->product && $variant->product->hasMedia('images')) {
                    $media = $variant->product->getFirstMedia('images');
                    $imageUrl = [
                        'id' => $media->id,
                        'original_url' => $media->getUrl(),
                    ];
                }
            }

            return [
                'id' => $order->id,
                'total' => $order->total,
                'status' => $order->status,
                'created_at' => $order->created_at->toDateTimeString(),
                'customer_reference' => $order->customer_reference ?? 'N/A',
                'items_count' => $order->lines->count(),
                'image_url' => $imageUrl,
                'description' => $description,

            ];
        });

        return response()->json(['orders' => $ordersWithMedia]);
    }

    
}

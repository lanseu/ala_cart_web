<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderLine;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function store(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'user_id' => $data['user_id'],
                'status' => $data['status'],
                'currency_code' => $data['currency_code'],
                'sub_total' => $data['sub_total'],
                'discount_total' => $data['discount_total'],
                'tax_total' => $data['tax_total'],
                'shipping_total' => $data['shipping_total'],
                'total' => $data['total'],
                'customer_reference' => $data['customer_reference'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['lines'] as $line) {
                $line['order_id'] = $order->id;
                OrderLine::create($line);
            }

            return $order->load('lines');
        });
    }

    public function getAll()
    {
        return Order::with('lines')->latest()->get();
    }

    public function getById(int $id)
    {
        return Order::with('lines')->findOrFail($id)->append('image_url');
    }
    
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $order = $this->getById($id);
            $order->lines()->delete();

            return $order->delete();
        });
    }
    public function getByUserId(int $userId)
    {
        return Order::with('lines')
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

}

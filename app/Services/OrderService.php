<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    const DISCOUNT_THRESHOLD = 4;
    const DISCOUNT_RATE      = 0.40;

    public function createOrder($userId, $addressId, array $items, $paymentMethod)
    {
        return DB::transaction(function () use ($userId, $addressId, $items, $paymentMethod) {
            $subtotal  = 0;
            $totalQty  = 0;

            foreach ($items as $item) {
                $product   = Product::findOrFail($item['product_id']);
                $unitPrice = $product->promo_price ?? $product->price;
                $subtotal  += $unitPrice * $item['quantity'];
                $totalQty  += $item['quantity'];
            }

            $discount    = $totalQty >= self::DISCOUNT_THRESHOLD ? (int)($subtotal * self::DISCOUNT_RATE) : 0;
            $totalAmount = max(0, $subtotal - $discount);

            $order = Order::create([
                'user_id'             => $userId,
                'delivery_address_id' => $addressId,
                'subtotal'            => $subtotal,
                'discount'            => $discount,
                'total_amount'        => $totalAmount,
                'status'              => 'pending',
                'payment_method'      => $paymentMethod,
                'payment_status'      => 'pending',
            ]);

            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $product->promo_price ?? $product->price,
                ]);
            }

            return $order->load('items.product');
        });
    }
}

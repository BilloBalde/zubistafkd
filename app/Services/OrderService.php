<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Services\DiscountService;

class OrderService
{
    public function createOrder($userId, $addressId, array $items, $paymentMethod)
    {
        return DB::transaction(function () use ($userId, $addressId, $items, $paymentMethod) {
            $discountService = app(DiscountService::class);
            $subtotal  = 0;
            $discount  = 0;

            $productIds = array_column($items, 'product_id');
            $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($items as $item) {
                $product   = $products[$item['product_id']];
                $unitPrice = $product->effective_price;
                $qty       = (int) $item['quantity'];
                $result    = $discountService->calculateItem($qty, $unitPrice);
                $subtotal += $unitPrice * $qty;
                $discount += $result['discount_amount'];
            }

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
                $product = $products[$item['product_id']];
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $product->effective_price,
                ]);
            }

            return $order->load('items.product');
        });
    }
}

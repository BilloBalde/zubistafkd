<?php

namespace App\Services;

class DiscountService
{
    public function calculateDiscountPercent(int $quantity): int
    {
        if ($quantity >= 20) return 10;
        if ($quantity >= 15) return 7;
        if ($quantity >= 10) return 5;
        if ($quantity >= 5)  return 3;
        return 0;
    }

    public function calculateItem(int $quantity, float $unitPrice): array
    {
        $discountPercent = $this->calculateDiscountPercent($quantity);
        $totalPrice      = $unitPrice * $quantity;
        $discountAmount  = $totalPrice * ($discountPercent / 100);
        $finalPrice      = $totalPrice - $discountAmount;

        return [
            'unit_price'       => $unitPrice,
            'quantity'         => $quantity,
            'discount_percent' => $discountPercent,
            'discount_amount'  => (int) round($discountAmount),
            'final_price'      => (int) round($finalPrice),
        ];
    }
}

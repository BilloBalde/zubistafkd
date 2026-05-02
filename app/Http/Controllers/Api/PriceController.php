<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\DiscountService;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function __construct(private DiscountService $discountService) {}

    public function calculate(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:9999',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Use promo price if currently active, otherwise regular price
        $unitPrice = (float) $product->price;
        if ($product->promo_price && (!$product->promo_ends_at || $product->promo_ends_at->isFuture())) {
            $unitPrice = (float) $product->promo_price;
        }

        $result = $this->discountService->calculateItem(
            (int) $request->quantity,
            $unitPrice
        );

        return response()->json($result);
    }
}

<?php

namespace App\Services;

use App\Models\StoreProduct;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Atomically decrement stock (PCS) if there is enough available.
     */
public function decrementIfAvailable(int $storeId, int $productId, int $quantity): bool
{
    if ($quantity <= 0) return true;

    $affected = DB::table('store_products')
        ->where('store_id', $storeId)
        ->where('product_id', $productId)
        ->where('quantity', '>=', $quantity)
        ->decrement('quantity', $quantity);

    return $affected > 0;
}

public function increment(int $storeId, int $productId, int $quantity): void
{
    if ($quantity <= 0) return;

    // Ensure the StoreProduct exists, or create a new row if it doesn't
    $row = StoreProduct::firstOrCreate(
        ['store_id' => $storeId, 'product_id' => $productId],
        ['quantity' => 0] // Default quantity of 0 if the row doesn't exist
    );

    // Increment the quantity of the product in the store
    $row->increment('quantity', $quantity);
}

    /**
     * Decrement cartons count if available, by StoreProduct row id.
     */
    public function decrementCtnsByRowIdIfAvailable(int $storeProductId, int $ctns): bool
    {
        if ($ctns <= 0) return true;

        $affected = StoreProduct::where('id', $storeProductId)
            ->where('ctns', '>=', $ctns)
            ->decrement('ctns', $ctns);

        return $affected > 0;
    }

    /**
     * Decrement quantity (PCS) if available, by StoreProduct row id.
     */
    public function decrementQtyByRowIdIfAvailable(int $storeProductId, int $quantity): bool
    {
        if ($quantity <= 0) return true;

        $affected = StoreProduct::where('id', $storeProductId)
            ->where('quantity', '>=', $quantity)
            ->decrement('quantity', $quantity);

        return $affected > 0;
    }
}


<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\DiscountService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $qty     = max(1, (int) $request->input('quantity', 1));
        $cart    = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $qty;
        } else {
            $cart[$id] = [
                "product_id" => $product->id,
                "name"       => $product->libelle,
                "quantity"   => $qty,
                "price"      => $product->effective_price,
                "image"      => $product->image,
            ];
        }

        session()->put('cart', $cart);

        if($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Produit ajouté au panier !',
                'count' => count($cart)
            ]);
        }

        return redirect()->back()->with('success', 'Produit ajouté au panier !');
    }

    public function show()
    {
        $cart            = session()->get('cart', []);
        $discountService = app(DiscountService::class);

        $subtotal      = 0;
        $totalDiscount = 0;
        $discountLines = [];

        foreach ($cart as $item) {
            $result         = $discountService->calculateItem((int) $item['quantity'], (float) $item['price']);
            $subtotal      += (float) $item['price'] * (int) $item['quantity'];
            $totalDiscount += $result['discount_amount'];

            if ($result['discount_percent'] > 0) {
                $discountLines[] = [
                    'name'    => $item['name'],
                    'qty'     => $result['quantity'],
                    'percent' => $result['discount_percent'],
                    'amount'  => $result['discount_amount'],
                ];
            }
        }

        $finalTotal = max(0, $subtotal - $totalDiscount);

        return view('ecommerce.cart', compact('cart', 'subtotal', 'totalDiscount', 'finalTotal', 'discountLines'));
    }

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (!isset($cart[$id])) {
            return redirect()->route('panier');
        }

        if ($request->action === 'increase') {
            $cart[$id]['quantity']++;
        } elseif ($request->action === 'decrease') {
            $cart[$id]['quantity']--;
            if ($cart[$id]['quantity'] <= 0) {
                unset($cart[$id]);
            }
        }

        session()->put('cart', $cart);
        return redirect()->route('panier');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        if(isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->back()->with('success', 'Produit retiré du panier.');
    }
}

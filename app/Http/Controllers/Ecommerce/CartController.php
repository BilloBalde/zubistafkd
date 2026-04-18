<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        // Si le produit est déjà dans le panier, on augmente la quantité
        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            // Sinon on l'ajoute
            $cart[$id] = [
                "product_id" => $product->id,
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image
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
        $cart = session()->get('cart', []);
        return view('ecommerce.cart', compact('cart'));
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

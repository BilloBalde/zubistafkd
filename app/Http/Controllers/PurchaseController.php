<?php

namespace App\Http\Controllers;

use App\Models\Logistic;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Store;
use App\Models\StoreProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.check');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $produits = Product::all();
        $boutiques = Store::all();
        $query = Purchase::query();

        if ($request->filled('numeroPurchase')) {
            $query->where('numeroPurchase', 'like', '%' . $request->input('numeroPurchase') . '%');
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', 'like', '%' . $request->input('product_id') . '%');
        }

        if ($request->filled('created_at')) {
            $query->where('created_at', $request->input('created_at'));
        }

        $dataTable = $query->get();

        // Pass the necessary data to the view, including options for filters
        return view('purchases.index', compact('dataTable', 'produits', 'boutiques'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ajout($numeroPurchase, $quantity, $store_id)
    {
        $products = Product::all();
        return view('purchases.create', compact('numeroPurchase', 'products', 'quantity', 'store_id'));
    }

    public function create()
    {
        return view('purchases.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
public function store(Request $request)
{
    $purchasesData = $request->input('purchases', []);

    $request->validate([
        'purchases' => 'required|array|min:1',
        'purchases.*.store_id' => 'required|exists:stores,id',
        'purchases.*.product_id' => 'required|exists:products,id',
        'purchases.*.price' => 'required|numeric|min:0',
        'purchases.*.price_ctn' => 'nullable|numeric|min:0',
        'purchases.*.quantity' => 'required|integer|min:1',
        'purchases.*.description' => 'nullable|string',
        'purchases.*.numeroPurchase' => 'required|string',
    ]);

    foreach ($purchasesData as $data) {

        // ✅ garantit une valeur pour description
        $data['description'] = $data['description'] ?? null;

        Purchase::create($data);

        StoreProduct::updateOrCreate(
            [
                'store_id' => $data['store_id'],
                'product_id' => $data['product_id'],
            ],
            [
                // ✅ incrément safe (pas de raw injection)
                'quantity' => DB::raw('quantity + ' . (int) $data['quantity']),
            ]
        );
    }

    return redirect()->route('purchases.index')
        ->with('success', 'Achat créé, et stock mis à jour avec succès');
}


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function show(Purchase $purchase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
{
    $purchase = Purchase::findOrFail($id);
    $products = Product::all();
    $stores = Store::all();  // <-- ici

    return view('purchases.edit', compact('purchase', 'products', 'stores'));
}
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $id)
    // {
    //     $purchase = Purchase::find($id);
    //     try {
    //         $logistic = Logistic::where('numeroPurchase', $purchase->numeroPurchase)->first();
    //         $reste = $purchase->quantity - $request->quantity;
    //         $logistic->quantity = $logistic->quantity - $reste;
    //         $logistic->save();
    //         StoreProduct::updateOrCreate(
    //             [
    //                 'store_id' => $purchase->store_id, // Assume store_id is part of the request data
    //                 'product_id' => $purchase->product_id,
    //             ],
    //             [
    //                 'quantity' => DB::raw("quantity - {$reste}"), // Increment the stock
    //             ]
    //         );
    //         $produit = Product::find($purchase->product_id);
    //         $oldReste = $produit->stock - $reste;
    //         $purchase->price = $request->price;
    //         $purchase->quantity = $request->quantity;
    //         $produit->stock = $oldReste;
    //         $produit->save();
    //         $purchase->save();
    //         return redirect()->route('purchases.index')->with('success', 'Achats modifié avec succès');
    //     } catch (\Throwable $th) {
    //         return redirect()->route('purchases.index')->with('error', 'Achats pas modifié parceque'. $th->getMessage());
    //     }

    // }
    public function update(Request $request, $id)
{
    $purchase = Purchase::findOrFail($id);

    try {
        DB::beginTransaction();

        $oldProductId = $purchase->product_id;
        $oldStoreId = $purchase->store_id;
        $oldQuantity = $purchase->quantity;

        $newProductId = $request->product_id;
        $newStoreId = $request->store_id;
        $newQuantity = $request->quantity;

        // 🎯 Si le produit ou le store a changé, ajuster les anciennes valeurs
        if ($oldProductId != $newProductId || $oldStoreId != $newStoreId) {
            // 1. Déduire l'ancienne quantité du stock précédent
            StoreProduct::where('store_id', $oldStoreId)
                ->where('product_id', $oldProductId)
                ->update([
                    'quantity' => DB::raw("GREATEST(quantity - $oldQuantity, 0)")
                ]);

            // 2. Ajouter la nouvelle quantité dans le nouveau store + produit
            StoreProduct::updateOrCreate(
                ['store_id' => $newStoreId, 'product_id' => $newProductId],
                ['quantity' => DB::raw("quantity + $newQuantity")]
            );
        } else {
            // 🎯 Sinon, mettre à jour en ajoutant la différence
            $diff = $newQuantity - $oldQuantity;
            StoreProduct::where('store_id', $oldStoreId)
                ->where('product_id', $oldProductId)
                ->update([
                    'quantity' => DB::raw("quantity + ($diff)")
                ]);
        }

        // 🎯 Mettre à jour la logistique liée
        $logistic = Logistic::where('numeroPurchase', $purchase->numeroPurchase)->first();
        if ($logistic) {
            $diff = $newQuantity - $oldQuantity;
            $logistic->quantity += $diff;
            $logistic->save();
        }

        // ✅ Mettre à jour tous les champs en une seule fois
        $purchase->product_id = $newProductId;
        $purchase->store_id = $newStoreId;
        $purchase->quantity = $newQuantity;
        $purchase->price = $request->price;
        $purchase->price_ctn = $request->price_ctn;
        $purchase->save();

        DB::commit();

        return redirect()->route('purchases.index')->with('success', 'Achat modifié avec succès.');
    } catch (\Throwable $th) {
        DB::rollBack();
        return redirect()->route('purchases.index')->with('error', 'Erreur lors de la modification : ' . $th->getMessage());
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return \Illuminate\Http\Response
     */
 public function destroy($id)
{
    $purchase = Purchase::findOrFail($id);

    try {
        DB::beginTransaction();

        $oldProductId = $purchase->product_id;
        $oldStoreId = $purchase->store_id;
        $oldQuantity = $purchase->quantity;

        // 🎯 1. Déduire la quantité du stock dans le store + produit associé
        StoreProduct::where('store_id', $oldStoreId)
            ->where('product_id', $oldProductId)
            ->update([
                'quantity' => DB::raw("GREATEST(quantity - $oldQuantity, 0)")
            ]);

        // 🎯 2. Réduire la quantité dans la logistique associée
        $logistic = Logistic::where('numeroPurchase', $purchase->numeroPurchase)->first();
        if ($logistic) {
            $logistic->quantity -= $oldQuantity;
            $logistic->save();
        }

        // 🎯 3. Supprimer l'achat
        $purchase->delete();

        DB::commit();

        return redirect()->route('purchases.index')->with('success', 'Achat supprimé avec succès.');
    } catch (\Throwable $th) {
        DB::rollBack();
        return redirect()->route('purchases.index')->with('error', 'Erreur lors de la suppression : ' . $th->getMessage());
    }
}

    public function exitAchat($numeroPurchase)
    {
        try {
            // Delete the Purchase and Logistic record
            Logistic::where('numeroPurchase', $numeroPurchase)->delete();

            return redirect()->route('purchases.index')->with('success', "Vous êtes parti sans valider, l'achat a été annulé.");
        } catch (\Throwable $th) {
            // Log the error and return with an error message
            return redirect()->back()->with('error', 'Impossible de quitter cette page, une erreur est survenue: ' . $th->getMessage());
        }
    }

}

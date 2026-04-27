<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Facture;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StoreProduct;
use App\Models\Sale;
use App\Models\Store;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
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
        if (auth()->user()->role_id == 3) {
            $boutiques = Store::where('user_id', auth()->user()->id)->get();
        } else {
            $boutiques = Store::all();
        }

        $query = Sale::query();

        if ($request->filled('numeroFacture')) {
            $query->where('numeroFacture', 'like', '%' . $request->input('numeroFacture') . '%');
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', 'like', '%' . $request->input('product_id') . '%');
        }

        if ($request->filled('created_at')) {
            $query->where('created_at', $request->input('created_at'));
        }

        $dataTable = $query->with('product', 'store')->get();

        // Pass the necessary data to the view, including options for filters
        $customers = Customer::all();
        return view('sales.index', compact('dataTable', 'produits','customers','boutiques'));
    }

    public function storeCustomer(Request $request)
    {
        $request->validate([
            'customerName' => 'required|string|max:255',
            'mark' => 'required|string|max:255',
            'tel' => 'required|string',
            'address' => 'required|string',
        ]);

        try {
            $customer = Customer::create([
                'customerName' => $request->customerName,
                'mark' => $request->mark,
                'tel' => $request->tel,
                'address' => $request->address,
                'fidelite' => 1
            ]);

            return response()->json([
                'success' => true,
                'customer' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sales.create');
    }

    public function ajout($numero_facture, $avance, $store_id)
    {
        $produits = Product::all();
        return view('sales.create', compact('numero_facture', 'produits', 'avance', 'store_id'));
    }

    public function voirSales($numero_facture){
        //dd('Here i am');
        $ligneVentes = Sale::where('numeroFacture', $numero_facture)->with('product')->get();
        return view('sales.voirSales', compact('ligneVentes', 'numero_facture'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

public function store(Request $request)
{
    $salesData = $request->input('sales');
    $rules = [
        'sales.*.numeroFacture' => 'required|string',
        'sales.*.product_id' => 'required|exists:products,id',
        'sales.*.prix' => 'required|numeric|min:0',
        'sales.*.quantity' => 'required|integer|min:1',
    ];

    //$validator = Validator::make($salesData, $rules);
    $validator = Validator::make($request->all(), $rules);   // ✅ correct
    if ($validator->fails()) {
        // Handle validation failure
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Prepare the data for insertion
    $total_quantity = 0;
    $total_price = 0;
    $i = 0;

    if (($request->final_total - $request->avance) < 0) {
        return redirect()->back()->with('error', 'Le montant de la commande est supérieur à la somme des avances, impossible donc de valider cette vente')->withInput();
    }

    DB::beginTransaction();
    try {
        $store = Store::findOrFail($request->store_id);
        $totalInteret = 0;

        foreach ($salesData as $data) {
            $qty              = (int) $data['quantity'];
            $data["prixTotal"] = $data['prix'] * $qty;

            // Stock par boutique
            DB::table('store_products')
                ->where('store_id', $request->store_id)
                ->where('product_id', $data['product_id'])
                ->decrement('quantity', $qty);

            // Stock global synchronisé
            Product::where('id', $data['product_id'])
                ->update(['pcs' => DB::raw("GREATEST(pcs - {$qty}, 0)")]);

            // Intérêt basé sur le dernier prix d'achat (le plus récent = le plus représentatif)
            $lastPurchase    = Purchase::where('product_id', $data['product_id'])->latest()->first();
            $prix_achat      = $lastPurchase ? $lastPurchase->price : 0;
            $data["interet"] = ($data['prix'] - $prix_achat) * $qty;
            $totalInteret   += $data['interet'];
            $total_quantity += $qty;
            $total_price    += $data['prixTotal'];

            Sale::create([
                'numeroFacture' => $request->numeroFacture,
                'product_id'    => $data['product_id'],
                'prix'          => $data['prix'],
                'quantity'      => $qty,
                'prixTotal'     => $data['prixTotal'],
                'interet'       => $data['interet'],
                'store_id'      => $request->store_id,
            ]);
            $i++;
        }

        // Une seule mise à jour du solde boutique après la boucle
        $store->increment('balance', $totalInteret);

        $reste = $total_price - $request->avance;

        if ($reste == 0) {
            $statut   = 'payé';
            $livraison = 'livré';
        } elseif ($request->avance > 0 && $reste > 0) {
            $statut   = 'partiel';
            $livraison = 'non livré';
        } else {
            $statut   = 'non payé';
            $livraison = 'non livré';
        }

        $facture = Facture::create([
            'numero_facture' => $request->numeroFacture,
            'store_id'       => $request->store_id,
            'customer_id'    => $request->customer_id,
            'montant_total'  => $total_price,
            'quantity'       => $total_quantity,
            'avance'         => $request->avance,
            'notes'          => $request->notes,
            'reste'          => $reste,
            'statut'         => $statut,
            'livraison'      => $livraison,
        ]);

        Payment::create([
            'facture_id' => $facture->id,
            'versement'  => $facture->avance,
            'total_paye' => $facture->avance,
            'paid_by'    => $request->paid_by,
            'reste'      => $reste,
            'note'       => "Premier versement de " . $facture->avance . " GNF effectué à l'émission de la facture.",
        ]);

        DB::commit();
        return redirect()->route('factures.index')->with('success', 'Vente créée, stock mis à jour avec succès.');

    } catch (\Throwable $th) {
        DB::rollBack();
        return redirect()->back()->with('fall', 'Erreur lors de la création : ' . $th->getMessage())->withInput();
    }
}



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function show(Sale $sale)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function edit(Sale $sale)
    {
        return view('sales.edit', compact('sale'));
    }

    public function pos(){
        $categories = Category::all();
        $produits = Product::all();
        $userStoreId = Auth::user()->role_id == 3
            ? Store::where('user_id', Auth::user()->id)->value('id')
            : null;
        $boutiques = Store::all();
        $customers = Customer::all();
        $countFactures = Facture::count() + 1;
        $numeroFacture = date('Ym').''.sprintf("%04d", $countFactures);
        return view('sales.pos', compact('produits', 'boutiques', 'customers', 'categories', 'userStoreId', 'numeroFacture'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $sale)
    {
        $sale = Sale::find($sale); // Use a meaningful variable name like $saleId instead of $sale
        if (!$sale) {
            return redirect()->route('sales.index')->with('error', 'Vente non trouvée.');
        }

        DB::beginTransaction();
        try {
            $invoice = Facture::where('numero_facture', $sale->numeroFacture)->first();
            if (!$invoice) {
                DB::rollBack();
                return redirect()->route('sales.index')->with('error', 'Facture non trouvée.');
            }

            $paiements      = Payment::where('facture_id', $invoice->id)->get();
            $oldQty         = $sale->quantity;
            $newQty         = (int) $request->quantity;
            $diffQty        = $oldQty - $newQty; // positif = on a vendu moins → remettre en stock
            $resteMontant   = ($oldQty * $sale->prix) - ($newQty * $request->prix);

            // Mettre à jour la facture
            $invoice->quantity      -= ($oldQty - $newQty);
            $invoice->montant_total -= $resteMontant;
            $invoice->reste          = max(0, $invoice->reste - $resteMontant);
            $invoice->save();

            // Mettre à jour le reste de chaque paiement
            foreach ($paiements as $pay) {
                $pay->reste = max(0, $pay->reste - $resteMontant);
                $pay->save();
            }

            // Remettre la différence de quantité en stock (boutique + global)
            if ($diffQty !== 0) {
                StoreProduct::updateOrCreate(
                    ['store_id' => $sale->store_id, 'product_id' => $sale->product_id],
                    ['quantity' => DB::raw('quantity + ' . $diffQty)]
                );
                // Pour le stock global : on remet si diffQty > 0, on enlève si diffQty < 0
                if ($diffQty > 0) {
                    Product::where('id', $sale->product_id)->increment('pcs', $diffQty);
                } else {
                    Product::where('id', $sale->product_id)
                        ->update(['pcs' => DB::raw('GREATEST(pcs - ' . abs($diffQty) . ', 0)')]);
                }
            }

            // Recalculer l'intérêt et mettre à jour le solde boutique
            $prixAchat   = Purchase::where('product_id', $sale->product_id)->latest()->first()?->price ?? 0;
            $ancienInteret = $sale->interet;
            $nouvelInteret = ($request->prix - $prixAchat) * $newQty;
            $diffInteret   = $nouvelInteret - $ancienInteret;

            if ($diffInteret != 0) {
                $store = Store::find($sale->store_id);
                $store?->increment('balance', $diffInteret);
            }

            $sale->prix      = $request->prix;
            $sale->quantity  = $newQty;
            $sale->prixTotal = $newQty * $request->prix;
            $sale->interet   = $nouvelInteret;
            $sale->save();

            DB::commit();
            return redirect()->route('sales.index')->with('success', 'Vente modifiée avec succès.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('sales.index')->with('error', 'La vente n\'a pas été modifiée : ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sale $sale)
    {
        DB::beginTransaction();
        try {
            // Remettre le stock en boutique
            StoreProduct::where('store_id', $sale->store_id)
                ->where('product_id', $sale->product_id)
                ->increment('quantity', $sale->quantity);

            // Remettre le stock global
            Product::where('id', $sale->product_id)->increment('pcs', $sale->quantity);

            // Reverser l'intérêt du solde boutique
            $store = Store::find($sale->store_id);
            if ($store && $sale->interet > 0) {
                $store->decrement('balance', $sale->interet);
            }

            // Mettre à jour la facture liée
            $invoice = Facture::where('numero_facture', $sale->numeroFacture)->first();
            if ($invoice) {
                $invoice->quantity      = max(0, $invoice->quantity - $sale->quantity);
                $invoice->montant_total = max(0, $invoice->montant_total - $sale->prixTotal);
                $invoice->reste         = max(0, $invoice->reste - $sale->prixTotal);

                // Recalculer le statut
                if ($invoice->montant_total <= 0) {
                    $invoice->delete();
                    Payment::where('facture_id', $invoice->id)->delete();
                } else {
                    if ($invoice->reste <= 0) {
                        $invoice->statut = 'payé';
                    } elseif ($invoice->avance > 0) {
                        $invoice->statut = 'partiel';
                    } else {
                        $invoice->statut = 'non payé';
                    }
                    $invoice->save();
                }
            }

            $sale->delete();
            DB::commit();

            return redirect()->route('sales.index')->with('success', 'Ligne de vente supprimée, stock restauré.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('sales.index')->with('error', 'Erreur lors de la suppression : ' . $th->getMessage());
        }
    }

    public function exitSale($numero_facture)
    {
        try {
            // Delete the Purchase and Logistic record
            Facture::where('numero_facture', $numero_facture)->delete();

            return redirect()->route('sales.index')->with('success', "Vous êtes parti sans valider, la vente a été annulé.");
        } catch (\Throwable $th) {
            // Log the error and return with an error message
            return redirect()->back()->with('error', 'Impossible de quitter cette page, une erreur est survenue: ' . $th->getMessage());
        }
    }
}

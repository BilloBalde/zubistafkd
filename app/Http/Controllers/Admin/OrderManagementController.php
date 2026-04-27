<?php
// app/Http/Controllers/Admin/OrderManagementController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Facture;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StoreProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderManagementController extends Controller
{
    // Affiche la liste des commandes en attente


    public function index()
    {
        $orders = Order::with(['user', 'items.product'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function stockCheck(Order $order, Request $request)
    {
        $order->load('items.product');

        $items = [];
        $allOk = true;

        foreach ($order->items as $item) {
            $product   = $item->product;
            $available = $product->pcs ?? 0;
            $ok        = $available >= $item->quantity;

            $items[] = [
                'name'      => $product->libelle ?? 'Produit #' . $product->id,
                'requested' => $item->quantity,
                'available' => $available,
                'ok'        => $ok,
            ];
            if (!$ok) $allOk = false;
        }

        return response()->json(['all_ok' => $allOk, 'items' => $items]);
    }
    // Affiche toutes les ventes confirmées par le manager
    public function confirmed(Request $request)
    {
        $query = Order::with(['user', 'items.product', 'facture', 'sales'])
            ->where('status', 'approved')
            ->latest();

        if ($request->filled('order_id')) {
            $query->where('id', $request->order_id);
        }
        if ($request->filled('client')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$request->client.'%'));
        }
        if ($request->filled('payment')) {
            $query->whereHas('facture', fn($q) => $q->where('statut', $request->payment));
        }

        $orders = $query->paginate(15);
        return view('admin.orders.confirmed', compact('orders'));
    }

  

    // Rejette une commande
    public function reject(Order $order)
    {
        if ($order->status !== 'pending') {

            if (request()->ajax()) {
                return response()->json(['error' => 'Déjà traitée'], 400);
            }

            return redirect()->back()->with('error', 'Cette commande a déjà été traitée.');
        }

        $order->update(['status' => 'rejected']);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.orders.index')->with('success', 'Commande rejetée.');
    }

    public function approve(Order $order, Request $request)
    {
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Commande déjà traitée.');
        }

        $storeId = $request->input('store_id');
        if (!$storeId) {
            return redirect()->back()->with('error', 'Veuillez sélectionner une boutique.');
        }

        $orderItems = $order->items;
        if ($orderItems->isEmpty()) {
            return redirect()->back()->with('error', 'Cette commande ne contient aucun article.');
        }

        $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);

        DB::beginTransaction();

        try {
            // Vérification du stock global (table products.pcs)
            foreach ($orderItems as $item) {
                $product   = \App\Models\Product::find($item->product_id);
                $available = $product->pcs ?? 0;

                if ($available < $item->quantity) {
                    throw new \Exception(
                        "Stock insuffisant pour « {$product->libelle} » (dispo: {$available}, demandé: {$item->quantity})"
                    );
                }
            }

            // Décrémentation du stock (boutique + global) et création des lignes de vente
            $totalQuantity = 0;
            $totalInteret  = 0;

            foreach ($orderItems as $item) {
                $qty = $item->quantity;

                // Décrémentation du stock global (products.pcs)
                \App\Models\Product::where('id', $item->product_id)
                    ->decrement('pcs', $qty);

                // Intérêt basé sur le dernier prix d'achat
                $lastPurchase = Purchase::where('product_id', $item->product_id)->latest()->first();
                $prixAchat    = $lastPurchase ? $lastPurchase->price : 0;
                $interet      = ($item->price - $prixAchat) * $qty;
                $totalInteret += $interet;

                Sale::create([
                    'numeroFacture' => $invoiceNumber,
                    'product_id'    => $item->product_id,
                    'store_id'      => $storeId,
                    'quantity'      => $qty,
                    'prix'          => $item->price,
                    'prixTotal'     => $qty * $item->price,
                    'interet'       => $interet,
                ]);
                $totalQuantity += $qty;
            }

            // Mettre à jour le solde de la boutique (intérêts e-commerce)
            if ($totalInteret != 0) {
                \App\Models\Store::where('id', $storeId)->increment('balance', $totalInteret);
            }

            // Création client, facture, paiement... (gardez votre code existant)
            $user = $order->user;
            $customer = Customer::firstOrCreate(
                ['email' => $user->email],
                [
                    'mark'         => strtoupper(substr($user->name, 0, 3)) . $user->id,
                    'customerName' => $user->name,
                    'tel'          => $user->phone ?? 'N/A',
                    'address'      => $order->address?->full_address ?? 'N/A',
                    'email'        => $user->email,
                ]
            );

            $isPaid = $order->payment_status === 'paid';
            $avance = $isPaid ? $order->total_amount : 0;
            $reste = $order->total_amount - $avance;
            $statut = $isPaid ? 'payé' : 'non payé';
            $paidBy = $order->payment_method === 'orange_money' ? 'orange money' : 'cash';

            $facture = Facture::create([
                'numero_facture' => $invoiceNumber,
                'customer_id'    => $customer->id,
                'store_id'       => $storeId,
                'quantity'       => $totalQuantity,
                'montant_total'  => $order->total_amount,
                'avance'         => $avance,
                'reste'          => $reste,
                'statut'         => $statut,
                'livraison'      => 'non livré',
                'notes'          => 'Commande e-commerce #' . $order->id,
            ]);

            if ($avance > 0) {
                Payment::create([
                    'facture_id' => $facture->id,
                    'versement'  => $avance,
                    'total_paye' => $avance,
                    'reste'      => 0,
                    'paid_by'    => $paidBy,
                    'note'       => 'Paiement ' . $order->payment_method . ' – Transaction: ' . ($order->transaction_id ?? 'N/A'),
                ]);
            }

            $order->update([
                'status' => 'approved',
                'invoice_number' => $invoiceNumber,
                'store_id' => $storeId
            ]);

            DB::commit();
            return redirect()->route('admin.orders.index')->with('success', 'Commande approuvée, vente enregistrée.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }
    /**
     * Récupère les articles d'une facture (pour livraison partielle)
     */
    public function getFactureItems(Facture $facture)
    {
        $order = $facture->order;
        $items = $order->items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_name' => $item->product->libelle ?? 'Produit #'.$item->product_id,
                'quantity' => $item->quantity,
                'quantity_delivered' => $item->quantity_delivered ?? 0,
            ];
        });
        return response()->json($items);
    }

    /**
     * Enregistre une livraison partielle (utilisation optionnelle)
     */
    public function partialDeliver(Request $request, Facture $facture)
    {
        $quantities = $request->input('quantities', []);
        foreach ($quantities as $itemId => $deliveredQty) {
            OrderItem::where('id', $itemId)->update([
                'quantity_delivered' => $deliveredQty
            ]);
        }
        $order = $facture->order;
        $totalDelivered = $order->items->sum('quantity_delivered');
        $totalOrdered = $order->items->sum('quantity');
        if ($totalDelivered >= $totalOrdered) {
            $facture->update(['livraison' => 'livré']);
        } else {
            $facture->update(['livraison' => 'partiellement livré']);
        }
        return response()->json(['success' => true]);
    }

    /**
     * Liste des factures générées par approbation de commandes e-commerce
     */
    public function factures(Request $request)
    {
        $query = Facture::with(['customer', 'store', 'paiements', 'order.user', 'order.items.product'])
            ->where('numero_facture', 'like', 'INV-%')
            ->latest();

        if ($request->filled('numero_facture')) {
            $query->where('numero_facture', 'like', '%' . $request->numero_facture . '%');
        }
        if ($request->filled('client')) {
            $query->whereHas('order.user', fn($q) => $q->where('name', 'like', '%' . $request->client . '%'));
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('livraison')) {
            $query->where('livraison', $request->livraison);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $factures = $query->paginate(15);
        return view('admin.orders.factures', compact('factures'));
    }

    /**
     * Afficher les détails d'une commande spécifique
     */
    public function show(Order $order)
    {
        // Charge les relations nécessaires pour la vue détail
        $order->load(['user', 'items.product', 'address', 'facture']);

        return view('admin.orders.show', compact('order'));
    }

}
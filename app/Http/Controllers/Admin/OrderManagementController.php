<?php
// app/Http/Controllers/Admin/OrderManagementController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Facture;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StoreProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderManagementController extends Controller
{
    // Affiche la liste des commandes en attente
    public function index()
    {
        $orders = Order::where('status', 'pending')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
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
            return redirect()->back()->with('error', 'Cette commande a déjà été traitée.');
        }

        $order->update(['status' => 'rejected']);
        $order->user->notify(new \App\Notifications\OrderStatusChanged($order));
        // TODO: Ajouter ici la logique d'envoi de notification au client

        return redirect()->route('admin.orders.index')->with('success', 'Commande rejetée.');
    }

    public function approve(Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Commande déjà traitée.');
        }
    
        $orderItems = $order->items; // doit être une collection
    
        if ($orderItems->isEmpty()) {
            return redirect()->back()->with('error', 'Cette commande ne contient aucun article.');
        }
    
        $storeId = $order->user->store_id ?? 1;
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
    
        DB::beginTransaction();
    
        try {
            // Vérification et diminution des stocks (comme avant)
            foreach ($orderItems as $item) {
                $storeProduct = StoreProduct::where('store_id', $storeId)
                    ->where('product_id', $item->product_id)
                    ->first();
                if (!$storeProduct || $storeProduct->quantity < $item->quantity) {
                    throw new \Exception("Stock insuffisant pour produit ID {$item->product_id}");
                }
                $storeProduct->decrement('quantity', $item->quantity);
            }
    
            // Insertion des lignes de vente
            $totalQuantity = 0;
            foreach ($orderItems as $item) {
                $lastPurchase = Purchase::where('product_id', $item->product_id)
                    ->latest()
                    ->first();
                $prixAchat = $lastPurchase ? $lastPurchase->price : 0;
                $interet   = ($item->price - $prixAchat) * $item->quantity;

                Sale::create([
                    'numeroFacture' => $invoiceNumber,
                    'product_id'    => $item->product_id,
                    'store_id'      => $storeId,
                    'quantity'      => $item->quantity,
                    'prix'          => $item->price,
                    'prixtotal'     => $item->quantity * $item->price,
                    'interet'       => $interet,
                ]);
                $totalQuantity += $item->quantity;
            }

            // Trouver ou créer le Customer correspondant à l'utilisateur
            $user     = $order->user;
            $customer = Customer::where('email', $user->email)->first();
            if (!$customer) {
                $customer = Customer::create([
                    'mark'         => strtoupper(substr($user->name, 0, 3)) . $user->id,
                    'customerName' => $user->name,
                    'tel'          => $user->phone ?? 'N/A',
                    'address'      => $order->address?->full_address ?? 'N/A',
                    'email'        => $user->email,
                ]);
            }

            // Statut paiement et avance
            $isPaid  = $order->payment_status === 'paid';
            $avance  = $isPaid ? $order->total_amount : 0;
            $reste   = $order->total_amount - $avance;
            $statut  = $isPaid ? 'payé' : 'non payé';
            $paidBy  = $order->payment_method === 'orange_money' ? 'orange money' : 'cash';

            // Créer la Facture
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

            // Créer le Paiement si la commande était déjà payée
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

            $order->update(['status' => 'approved', 'invoice_number' => $invoiceNumber]);
            //$order->user->notify(new \App\Notifications\OrderStatusChanged($order));
    
            DB::commit();
            return redirect()->route('admin.orders.index')->with('success', 'Commande approuvée, vente enregistrée.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

}
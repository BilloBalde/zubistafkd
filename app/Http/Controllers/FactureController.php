<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Facture;
use App\Models\Payment;
use App\Models\Store;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FactureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.check');
    }

    public function index(Request $request){
        $customers = Customer::all();
        $query = Facture::query();

        if ($request->filled('numero_facture')) {
            $query->where('numero_facture', 'like', '%' . $request->input('numero_facture') . '%');
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', 'like', '%' . $request->input('customer_id') . '%');
        }

        if ($request->filled('statut')) {
            $query->where('statut', 'like', '%' . $request->input('statut') . '%');
        }

        if ($request->filled('livraison')) {
            $query->where('livraison', 'like', '%' . $request->input('livraison') . '%');
        }

        if ($request->filled('created_at')) {
            $query->where('created_at', $request->input('created_at'));
        }

        if (auth()->user()->role_id == 3) {
            $dataTable = $query->where('store_id', Store::where('user_id', auth()->user()->id)->first()->id)->get();
        } else {
            $dataTable = $query->get();
        }

        return view('factures.index', compact('dataTable', 'customers',));
    }

    public function show($facture){
        $user = User::where('role_id', 2)->first();
        $invoice = Sale::where('numeroFacture', $facture)->get();
        $laFacture = Facture::where('numero_facture', $facture)->first();
        $customer = Customer::where('id', $laFacture->customer_id)->first();
        $paiements = Payment::where('facture_id', $laFacture->id)->get();
        return view('factures.show', compact('invoice', 'facture', 'laFacture', 'customer', 'user', 'paiements'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'numero_facture' => 'required|unique:factures',
            'customer_id' => 'required',
            'avance' => 'required|numeric',
            'notes' => 'nullable|string',
        ],[
            'numero_facture.required' => 'Le champ numéro de la facture est obligatoire',
            'numero_facture.unique' => 'Le champ numéro de la facture doit être unique',
            'customer_id.required' => 'Veuillez selectionner le client',
            'avance.required' => 'Veuillez entrer un montant, à la rigueure 0',
        ]);

        if (isset($request->customerName) && isset($request->mark) && isset($request->tel) && isset($request->email) && isset($request->address)) {
            $customer = Customer::create([
                'customerName' => $request->customerName,
                'mark' => $request->mark,
                'tel' => $request->tel,
                'email' => $request->email,
                'address' => $request->address,
            ]);

            $customer_id = $customer->id;
        }
        try {
            Facture::create([
                'numero_facture' => $request->numero_facture,
                'store_id' => $request->store_id,
                'customer_id' => $customer_id ?? $request->customer_id,
                'avance' => $request->avance,
                'notes' => $request->notes,
                'statut' => 'pending',
                'livraison' => 'non livré',
            ]);
            $sms = "Facture cree avec success";
            $countCustomerInvoices = Facture::where('customer_id', $request->customer_id)->count();
            if ($countCustomerInvoices >= 5){
                $customer = Customer::find($request->customer_id);
                $customer->fidelite = 1;
                $customer->save();
                $sms = "Facture cree avec success. Merci au client ".$customer->mark." pour sa fidelity";
            }
            // Redirect back with a success message
            $products = Product::all();
            return redirect()->route('sales.ajout',[$request->numero_facture, $request->avance, $request->store_id])->with([
                'success'=>$sms,
                'numeroFacture'=> $request->numero_facture,
                'products'=> $products,
                'avance' => $request->avance
            ]);
        } catch (\Throwable $th) {
            return back()->with('fall', 'une erreur lors de lajout, voici le message : '.$th->getMessage());
        }
    }

    public function edit($id){
        $facture = Facture::find($id);
        return view('factures.edit', compact('facture'));
    }

    public function update(Facture $facture){
        try {
            $facture->livraison = 'livré';
            $facture->save();
            return redirect()->back()->with('success', 'Votre facture a été mis à jour: livré');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'erreur lors de la modification'.$th->getMessage());
        }
    }
}

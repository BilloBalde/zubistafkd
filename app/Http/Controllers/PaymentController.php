<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Facture;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
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
        $factures = Facture::all();
        $query = Payment::query();

        if ($request->filled('facture_id')) {
            $query->where('facture_id', 'like', '%' . $request->input('facture_id') . '%');
        }

        if ($request->filled('paid_by')) {
            $query->where('paid_by', 'like', '%' . $request->input('paid_by') . '%');
        }

        if ($request->filled('created_at')) {
            $query->where('created_at', $request->input('created_at'));
        }

        $dataTable = $query->get();
        return view('payments.index', compact('dataTable', 'factures'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function creation($id){
        $facture = Facture::find($id);
        // dd($depot);
        return view('factures.addPayment', compact('facture'));
    }

    public function voir($id){
        $facture = Facture::find($id);
        // dd($depot);
        return view('factures.showPayment', compact('facture'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paid_by' => 'required',
            'versement' => 'required|numeric',
            'note' => 'nullable|string',
        ],[
            'paid_by.required' => 'Veuillez selectionner le moyen de paiement',
            'versement.required' => 'Veuillez entrer un montant, à la rigueure 0',
        ]);

        try {
            $old = Payment::where('facture_id', $request->facture_id)->orderBy('id', 'DESC')->first();
            $total_paye = $old->total_paye + $request->versement;
            $reste = $old->reste - $request->versement;
            if ($reste >= 0) {
               // dd($old);
                Payment::create([
                    "facture_id" => $request->facture_id,
                    "versement" => $request->versement,
                    "paid_by" => $request->paid_by,
                    "note" => $request->note,
                    "total_paye" => $total_paye,
                    "reste" => $reste
                ]);
            }
            //dd($old, $total_paye, $reste);
            if($reste == 0){
                $factu = Facture::where('id', $request->facture_id);
                $factu->update([
                    'statut'=>'payé',
                    'reste'=>$reste
                ]);
                $client_id = $factu->first()->customer_id;
                $client = Customer::where('id',$client_id)->first()->customerName;
                $sms = "Facture ".$factu->first()->numero_facture." mis à jour et le montant total a été payée, merci au client ".$client;
                session(['success' => $sms]);
                return redirect()->route('factures.index');
            }
            elseif ($reste > 0) {
                $factu = Facture::where('id',$request->facture_id);
                $factu->update([
                    'statut'=>'partiel',
                    'reste'=>$reste
                ]);
                $sms = "Le montant de ".$request->versement." a été versé au compte de la facture ".$factu->first()->numero_facture.".";
                session(['success' => $sms]);
                return redirect()->route('factures.index');
            } else {
                $sms = "Le montant ne peut pas être négatif ou supérieur au montant total";
                session(['success' => $sms]);
                return redirect()->route('factures.index');
            }

        } catch (\Throwable $th) {
            return back()->with('fall', 'une erreur lors de lajout, voici le message : '.$th->getMessage());
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        //
    }
}

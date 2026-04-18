<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
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
    public function index()
    {
        $dataTable = Customer::all();
        return view('customers.index', compact('dataTable'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function validation(Request $request){
        $request->validate([
            'customerName'=>'required|max:250',
            'tel'=>'required|max:18',
            'mark' => 'required',
            'address' => 'required|string',
        ],[
            'customerName.required' => 'champ nom doit etre rempli.',
            'customerName.max' => 'champ nom prend maximum 250 charactere.',
            'tel.require' => 'champ tel doit etre rempli.',
            'tel.max' => 'champ tel prend maximum 18 caracteres.',
            'mark.require' => 'champ mark doit etre rempli.',
            'address.require' => 'champ address doit etre rempli.',
            'address.string' => 'champ address prend que des caracteres.',
        ]);
    }

    public function store(Request $request)
    {
        $this->validation($request);
        try {
            Customer::updateOrCreate(['id' => $request->id], [
                'customerName' => $request->customerName,
                'tel' => $request->tel,
                'address' => $request->address,
                'mark' => $request->mark,
            ]);

            return redirect()->route('customers.index')->with('success', 'Client ajouté avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('customers.index')->with('error', 'Erreur lors de l\'ajout du client.');
        }
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $customers = Customer::where('customerName', 'LIKE', "%{$search}%")
                            ->orWhere('mark', 'LIKE', "%{$search}%")
                            ->get(['id', 'customerName', 'mark']);
        if ($customers->isEmpty()) {
            return response()->json(['status' => 'no_results']);
        }

        return response()->json(['status' => 'found', 'customers' => $customers]);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = Customer::find($id);
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

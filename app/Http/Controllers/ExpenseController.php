<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Store;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
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
        $stores = Store::all();  // Get all stores

        $query = Expense::query();
        $categories_expenses = ExpenseCategory::all();
        if ($request->filled('reference')) {
            $query->where('reference', 'like', '%' . $request->input('reference') . '%');
        }

        if ($request->filled('expense_categories_id')) {
            $query->where('expense_categories_id', 'like', '%' . $request->input('expense_categories_id') . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', 'like', '%' . $request->input('status') . '%');
        }

        if ($request->filled('amount')) {
            $query->where('amount', 'like', '%' . $request->input('amount') . '%');
        }

        if ($request->filled('created_at')) {
            $query->where('created_at', $request->input('created_at'));
        }

        $expenses = $query->get();
        return view('expenses.index', compact('expenses', 'categories_expenses','stores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
{
    // Get all expense categories and stores
    $categories = ExpenseCategory::all();
    $stores = Store::all();

    // Generate the reference number for the expense
    $ref = "DEP" . Carbon::now()->format('Ym') . sprintf("%04d", Expense::count() + 1);

    // Check if the logged-in user is user_id = 2 (or has the appropriate role)

    // Pass data to the view, including the showUkexpense flag
    return view('expenses.create', compact('categories', 'ref', 'stores'));
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {

    //     $validated =  $request->validate([
    //         'reference' => 'required|string|max:255|unique:expenses,reference',
    //         'expense_categories_id' => 'required|exists:expense_categories,id',
    //         'amount' => 'required|numeric',
    //         'status' => 'required|string',
    //         'description' => 'nullable|string',
    //         'store_id' => 'required|exists:stores,id',  // Ensure the store exists

    //     ],[
    //         'reference.required' => 'Le champ reference est obligatoire',
    //         'reference.string' => 'Le champ reference prend que des chaines de caractères',
    //         'reference.max' => 'Le champ reference prend au maximum 255 chaines de caractères',
    //         'reference.unique' => 'Le champ reference doit être unique, veuillez changer',
    //         'expense_categories_id.required' => 'Le champ expense_categories_id est obligatoire',
    //         'expense_categories_id.exists' => 'Le champ expense_categories_id doit être selectionné dans la liste',
    //         'amount.required' => 'Le champ amount est obligatoire',
    //         'amount.numeric' => 'Le champ amount prend que des chiffres',
          
    //         'description.string' => 'Le champ description prend que des chaines de caractères',
    //     ]);
    //   // Find the store and associated interet
    //   $store = Store::findOrFail($request->store_id);
        
    //   // Start a database transaction to ensure atomicity
    //   DB::beginTransaction();
    //   try {
    //       // Create the expense record
    //       $expense = Expense::create([
    //           'reference' => $validated['reference'],
    //           'expense_categories_id' => $validated['expense_categories_id'],
    //           'store_id' => $validated['store_id'],
    //           'amount' => $validated['amount'],
    //           'ukexpense' => $validated['ukexpense'] ?? null, // Save the ukexpense value if it is provided
    //           'description' => $validated['description'],
    //       ]);
    //         // Deduct the expense amount from the store's balance
    //         $store->balance -= $expense->amount;  // Deduct the amount
    //         $store->save();  // Save the updated store balance
    //       // Commit the transaction
    //       DB::commit();

    //       // Return success message
    //       return redirect()->route('expenses.index')->with('success', 'Expense created successfully and balance updated.');
    //   } catch (\Exception $e) {
    //       // Rollback the transaction if an error occurs
    //       DB::rollBack();
    //       return back()->with('error', 'Error occurred: ' . $e->getMessage());
    //   }
    // }

    public function store(Request $request)
    {
        // Validate request data
        $validated =  $request->validate([
            'reference' => 'required|string|max:255|unique:expenses,reference',
            'description' => 'nullable|string',
            'exp_mode' => 'required|in:ukexpense,others',  // Validate the exp_mode
            'store_id' => 'required_if:exp_mode,others|exists:stores,id',  // Only validate store_id if exp_mode is 'others'

        ], [
            'reference.required' => 'Le champ reference est obligatoire',
            'reference.string' => 'Le champ reference prend que des chaines de caractères',
            'reference.max' => 'Le champ reference prend au maximum 255 chaines de caractères',
            'reference.unique' => 'Le champ reference doit être unique, veuillez changer',
            'expense_categories_id.required' => 'Le champ expense_categories_id est obligatoire',
            'expense_categories_id.exists' => 'Le champ expense_categories_id doit être selectionné dans la liste',
            'exp_mode.required' => 'Le champ mode de dépense est obligatoire',
            'exp_mode.in' => 'Le mode de dépense doit être soit "ukexpense" soit "others"',
            'description.string' => 'Le champ description prend que des chaines de caractères',
            'store_id.required_if' => 'Le champ store est obligatoire pour "others"',
            'store_id.exists' => 'Le champ store doit être valide',
         
        ]);
    
        // Find the store and associated balance if exp_mode is 'others'
        $store = Store::findOrFail($request->store_id);
        // Start a database transaction
        DB::beginTransaction();
        try {
            $expenseData = [
                'reference' => $request->reference,
                'expense_categories_id' => $request->expense_categories_id ?? 0, // Default to 0 if not provided
                'store_id' =>  $request->store_id, // Only save store_id for 'others'
                'amount' => $request->amount, // Only save amount for 'others'
                'exp_mode' => $request->exp_mode,
                'description' => $request->description
            ];
            //dd($expenseData);
            $expense = Expense::create($expenseData);
    
            // If the mode is 'others', deduct the expense amount from the store's balance
            if ($request->exp_mode == 'others') {
                $store->balance -= $request->amount;
                $store->save();
            }else{
                $store->balance += $request->amount;
                $store->save();
            }
    
            // Commit the transaction
            DB::commit();
    
            // Return success message
            return redirect()->route('expenses.index')->with('success', 'Expense created successfully and balance updated.');
        } catch (\Exception $e) {
            // Rollback the transaction if an error occurs
            DB::rollBack();
            return back()->with('error', 'Error occurred: ' . $e->getMessage());
        }
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
    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::all();
        $stores = Store::all();
        return view('expenses.create', compact('expense', 'categories','stores'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
 
    public function update(Request $request, Expense $expense)
{
    // Validate the input data
    $request->validate([
        'reference' => 'required|string|max:255|unique:expenses,reference,' . $expense->id,
        'expense_categories_id' => 'required|exists:expense_categories,id',
        'amount' => 'required|numeric',
        'status' => 'required|string',
        'description' => 'nullable|string',
    ], [
        'reference.required' => 'Le champ reference est obligatoire',
        'reference.string' => 'Le champ reference prend que des chaines de caractères',
        'reference.max' => 'Le champ reference prend au maximum 255 chaines de caractères',
        'reference.unique' => 'Le champ reference doit être unique, veuillez changer',
        'expense_categories_id.required' => 'Le champ expense_categories_id est obligatoire',
        'expense_categories_id.exists' => 'Le champ expense_categories_id doit être selectionné dans la liste',
        'amount.required' => 'Le champ amount est obligatoire',
        'amount.numeric' => 'Le champ amount prend que des chiffres',
        'status.required' => 'Le champ status est obligatoire',
        'status.string' => 'Le champ status prend que des chaines de caractères',
        'description.string' => 'Le champ description prend que des chaines de caractères',
    ]);

    // Start a database transaction to ensure atomicity
    DB::beginTransaction();

    try {
        // Get the current amount of the expense before updating
        $oldAmount = $expense->amount;
        
        // Update the expense record with the new values
        $expense->update($request->all());

        // Find the store related to the expense
        $store = $expense->store; // Assuming Expense has a relationship with Store

        // Calculate the difference between the old amount and the new amount
        $amountDifference = $request->amount - $oldAmount;

        // Update the store's balance (add or subtract the difference)
        $store->balance += $amountDifference;

        // Save the updated store balance
        $store->save();

        // Commit the transaction
        DB::commit();

        // Redirect back with success message
        return redirect()->route('expenses.index')->with('success', 'Expense mis à jour avec succès et le solde du magasin mis à jour.');

    } catch (\Throwable $th) {
        // Rollback the transaction if an error occurs
        DB::rollBack();

        // Return an error message
        return redirect()->route('expenses.index')->with('error', 'Erreur lors de la mise à jour de cette dépense. '.$th->getMessage());
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expense $expense)
    {
        // Start a transaction to ensure atomicity
        DB::beginTransaction();
    
        try {
            // Find the store associated with the expense
            $store = $expense->store; // Assuming Expense has a relationship with Store
    
            // Add the expense amount back to the store's balance (reverse the deduction)
            $store->balance += $expense->amount;
    
            // Save the updated store balance
            $store->save();
    
            // Now delete the expense
            $expense->delete();
    
            // Commit the transaction
            DB::commit();
    
            // Return success response
            return redirect()->route('expenses.index')->with('success', 'Expense supprimé avec succès et le solde du magasin mis à jour.');
    
        } catch (\Throwable $th) {
            // Rollback the transaction if any error occurs
            DB::rollBack();
    
            // Return error response
            return redirect()->route('expenses.index')->with('error', 'Expense non supprimé à cause de cette erreur. '.$th->getMessage());
        }
    }
}

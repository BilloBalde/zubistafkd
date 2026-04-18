<?php

namespace App\Http\Controllers;

use App\Models\StoreProduct;
use App\Models\StockTransfer;
use App\Models\Store;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreProductController extends Controller
{
    private StockService $stock;

    public function __construct(StockService $stock)
    {
        $this->middleware('auth.check');
        $this->stock = $stock;
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
        $query = StockTransfer::query();

        if ($request->filled('from_store_id')) {
            $query->where('from_store_id', 'like', '%' . $request->input('from_store_id') . '%');
        }

        if ($request->filled('to_store_id')) {
            $query->where('to_store_id', 'like', '%' . $request->input('to_store_id') . '%');
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', 'like', '%' . $request->input('product_id') . '%');
        }

        if ($request->filled('created_at')) {
            $query->where('created_at', $request->input('created_at'));
        }

        $dataTable = $query->get();

        // Pass the necessary data to the view, including options for filters
        return view('transfers.index', compact('dataTable', 'produits', 'boutiques'));
    }
public function transfer(StockTransfer $transfer)
{
    $transfer->load(['product', 'fromStore', 'toStore']);

    // Get the store associated with the user
    $store = auth()->user()->store;

    // Check if the store exists, if not, handle the null case
    if (!$store) {
        // You can either set a default value for receipt_number or handle the case gracefully
        $receiptNumber = 'N/A'; // Or handle as needed
    } else {
        // Generate a receipt number based on the date and an incremented number
        // Format the date part of the receipt number (e.g., '2026-02-25')
        $datePart = now()->format('Y-m-d');

        // Get the latest receipt number for today, if any
        $latestReceipt = StockTransfer::whereDate('created_at', now()->toDateString())
                                      ->orderByDesc('id')
                                      ->first();

        // Generate the incremented part (default to 1 if no previous transfer exists today)
        $incrementPart = $latestReceipt ? str_pad((int) substr($latestReceipt->receipt_number, -5) + 1, 5, '0', STR_PAD_LEFT) : '00001';

        // Combine the date and incremented number to create the receipt number
        $receiptNumber = $datePart . '-' . $incrementPart;
    }

    return view('transfers.transfer', [
        'transfer' => $transfer,
        'store' => $store, // Pass store instead of company
        'receiptNumber' => $receiptNumber,
        'user' => auth()->user(),
    ]);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function store(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'from_store_id' => 'required|exists:stores,id',
        'to_store_id' => 'required|exists:stores,id|different:from_store_id',
        'quantity' => 'required|integer|min:1',
    ]);

    try {
        $productId = (int) $request->product_id;
        $fromStoreId = (int) $request->from_store_id;
        $toStoreId = (int) $request->to_store_id;
        $quantity = (int) $request->quantity;

        DB::beginTransaction();

        // Atomic decrement on source store to avoid negative stock or concurrency issues
        if (!$this->stock->decrementIfAvailable($fromStoreId, $productId, $quantity)) {
            DB::rollBack();
            return redirect()->route('transfers.index')->with('error', 'Stock insuffisant dans le magasin source.');
        }

        // Increment the stock in the destination store
        $this->stock->increment($toStoreId, $productId, $quantity);

        // Record the stock transfer
        StockTransfer::create([
            'product_id' => $productId,
            'from_store_id' => $fromStoreId,
            'to_store_id' => $toStoreId,
            'quantity' => $quantity,
        ]);

        DB::commit();
        return redirect()->route('transfers.index')->with('success', 'Le transfert a été effectué avec succès.');
    } catch (\Throwable $th) {
        DB::rollBack();
        return redirect()->route('transfers.index')->with('error', 'Erreur: ' . $th->getMessage());
    }
}

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StoreProduct  $storeProduct
     * @return \Illuminate\Http\Response
     */
    public function show(StoreProduct $storeProduct)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\StoreProduct  $storeProduct
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $storeProduct = StockTransfer::find($id);
        $produits = Product::all();
        $boutiques = Store::all();
        return view('transfers.edit', compact('storeProduct', 'produits', 'boutiques'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StoreProduct  $storeProduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id' => 'required|exists:stores,id|different:from_store_id',
            'quantity' => 'required|integer|min:1',
        ]);
        try {
            // Fetch the existing transfer record
            $transfer = StockTransfer::findOrFail($id);

            $productId = (int) $request->product_id;
            $fromStoreId = (int) $request->from_store_id;
            $toStoreId = (int) $request->to_store_id;
            $newQuantity = (int) $request->quantity;
            $oldQuantity = (int) $transfer->quantity;

            DB::beginTransaction();

            // 1) Reverse original transfer safely:
            // source += oldQuantity
            $this->stock->increment($fromStoreId, $productId, $oldQuantity);

            // destination -= oldQuantity (must have enough)
            if (!$this->stock->decrementIfAvailable($toStoreId, $productId, $oldQuantity)) {
                DB::rollBack();
                return redirect()->route('transfers.index')->with('error', 'Stock insuffisant dans le magasin destination pour annuler l\'ancien transfert.');
            }

            // 2) Apply new transfer safely:
            if (!$this->stock->decrementIfAvailable($fromStoreId, $productId, $newQuantity)) {
                DB::rollBack();
                return redirect()->route('transfers.index')->with('error', 'Stock insuffisant dans le magasin source pour appliquer le nouveau transfert.');
            }

            $this->stock->increment($toStoreId, $productId, $newQuantity);

            // 3) Update transfer record
            $transfer->update([
                'product_id' => $productId,
                'from_store_id' => $fromStoreId,
                'to_store_id' => $toStoreId,
                'quantity' => $newQuantity,
            ]);

            DB::commit();
            return redirect()->route('transfers.index')->with('success', 'Le transfert a été mis à jour avec succès.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('transfers.index')->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StoreProduct  $storeProduct
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
{
    try {
        // Fetch the existing transfer record
        $transfer = StockTransfer::findOrFail($id);

        $productId = (int) $transfer->product_id;
        $fromStoreId = (int) $transfer->from_store_id;
        $toStoreId = (int) $transfer->to_store_id;
        $quantity = (int) $transfer->quantity;

        DB::beginTransaction();

        // 1) Reverse the transfer:
        // source += quantity (increment back to source store)
        $this->stock->increment($fromStoreId, $productId, $quantity);

        // destination -= quantity (decrement back from destination store)
        if (!$this->stock->decrementIfAvailable($toStoreId, $productId, $quantity)) {
            DB::rollBack();
            return redirect()->route('transfers.index')->with('error', 'Stock insuffisant dans le magasin destination pour annuler le transfert.');
        }

        // 2) Delete the transfer record
        $transfer->delete();

        DB::commit();
        return redirect()->route('transfers.index')->with('success', 'Le transfert a été supprimé avec succès.');
    } catch (\Throwable $th) {
        DB::rollBack();
        return redirect()->route('transfers.index')->with('error', 'Une erreur est survenue : ' . $th->getMessage());
    }
}
}

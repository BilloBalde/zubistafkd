<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
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
        // Fetch all categories to display in the filter dropdown
        $categories = Category::all();

        // Start the query for products
        $query = Product::query();

        // Filter by product name (libelle)
        if ($request->filled('libelle')) {
            $query->where('libelle', 'like', '%' . $request->input('libelle') . '%');
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->input('category_id'));
            });
        }

        // Fetch all products with their categories and stores
        $allProducts = $query->with('categories', 'stores')->get();

        // Fetch the connected user's store ID if they have role_id == 3
        $userStoreId = Auth::user()->role_id == 3
            ? Store::where('user_id', Auth::user()->id)->value('id')
            : null;

        // Pass the necessary data to the view, including options for filters and current search data
        return view('products.index', compact('allProducts', 'categories', 'userStoreId'))
            ->with('libelle', $request->input('libelle'))
            ->with('category_id', $request->input('category_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function validation(Request $request){
        return $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id', // Ensure each category exists
            'libelle' => 'required|string|max:225',
            'sku' => 'required|string|max:225',
            'description' => 'required|string',
            'image' => 'image',
            'price_carton' => 'required|numeric',
            'price' => 'required|numeric',
            'pcs' => 'required|integer',
        ],[
            'categories.required' => 'veuillez selectionner la categorie',
            'categories.array' => 'Selectionner plusieurs categories',
            'libelle.required' => 'champ libelle doit être rempli',
            'libelle.string' => 'champ libelle prend au maximum 225 caractères',
            'sku.required' => 'champ sku doit être rempli',
            'sku.string' => 'champ sku prend au maximum 225 caractères',
            'description.required' => 'champ description doit être rempli avec une chaine de caractere',
            'image.image' => 'champ image ne prend que des images',
            'price.required' => 'Le champ prix est obligatoire.',
            'price.numeric' => 'Le champ prix doit contenir uniquement des chiffres.',
            'pcs.required' => 'Le champ pcs est obligatoire.',
            'pcs.integer' => 'Le champ pcs doit contenir uniquement des chiffres.',
            'price_caton.required' => 'Le champ prix_caton est obligatoire.',
        ]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validation($request);
        if(request()->hasfile('image')){
            $productName = time().'.'.request()->image->getClientOriginalExtension();
            request()->image->move(public_path('products'), $productName);
        }
        try{
            // Create the product

            $product = Product::create([
                'libelle' => $request->libelle,
                'sku' => $request->sku,
                'description' => $request->description,
                'image' => $productName ?? NULL,
                 'price' => $request->price,
                'pcs' => $request->pcs,
                'price_carton' => $request->price_carton,
            ]);

            // Attach the selected categories
            $product->categories()->attach($request->categories);
            return redirect()->route('produits.index')->with('success', 'Produit crée avec succès.');
        }
        catch(\Exception $e) {
            return back()->with('fall', 'une erreur lors de lajout, voici le message : '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        $categories = Category::all();
        return view('products.edit', compact('product','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'libelle' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,' . $id,
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $product = Product::findOrFail($id);
            $product->update($request->except('categories', 'image'));

            // Handle image upload
            if ($request->hasFile('image')) {
                $productName = time().'.'.request()->image->getClientOriginalExtension();
                request()->image->move(public_path('products'), $productName);
                $product->update(['image' => $productName]);
            }

            // Sync categories
            $product->categories()->sync($request->categories);

            return redirect()->route('produits.index')->with('success', 'Produit mis à jour avec succès!');
        }
        catch(\Exception $e) {
            return back()->with('fall', 'une erreur lors de lajout, voici le message : '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //dd($id);
        try {
            Product::find($id)->delete();
            return redirect()->back()->with('success', 'Produit supprimé avec succès.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Produit pas supprimé. Voici l\'erreur'. $th->getMessage());
        }

    }
}

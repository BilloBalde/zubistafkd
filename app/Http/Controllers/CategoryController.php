<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.check');
    }
    
    public function index()
    {
        $grouped = Category::orderBy('category_type')->orderBy('slug')->get()
            ->groupBy('category_type');
        return view('categories.index', compact('grouped'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|unique:categories,slug',
            'category_type' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ],[
            'slug.required' => 'L\'identifiant est réquis',
            'slug.unique' => 'L\'identifiant doit être unique, il existe déjà',
            'category_type.required' => 'Le type de la categorie est requise.',
            'category_type.string' => 'Le type de la catégorie doit être une chaine de charactère.',
            'description.required' => 'La description de la categorie est requise.',
            'description.string' => 'La description de la catégorie doit être une chaine de charactère.',
        ]);

        try {
            $data = $request->only(['slug', 'category_type', 'description']);
            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->image->getClientOriginalExtension();
                $request->image->move(public_path('categories'), $imageName);
                $data['image'] = $imageName;
            }
            Category::create($data);
            return redirect()->route('categories.index')->with('success', 'Category crée avec succès.');
        } catch (\Throwable $th) {
            return redirect()->route('categories.index')->with('error', 'Error lors de la creation de la categorie.'.$th->getMessage());
        }
    }

    // public function show(CategoryEmballage $categoryEmballage)
    // {
    //     //
    // }

    public function edit($categoryEmballage)
    {
        $categoryEmballage = Category::find($categoryEmballage);
        return view('categories.create', compact('categoryEmballage'));
    }

    public function update(Request $request, $categoryEmballage)
    {
        $request->validate([
            'slug' => 'required',
            'category_type' => 'required',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
        $categoryEmballage = Category::findOrFail($categoryEmballage);
        try {
            $data = $request->only(['slug', 'category_type', 'description']);
            if ($request->hasFile('image')) {
                $imageName = time() . '.' . $request->image->getClientOriginalExtension();
                $request->image->move(public_path('categories'), $imageName);
                $data['image'] = $imageName;
            }
            $categoryEmballage->update($data);
            return redirect()->route('categories.index')->with('success', 'Category modifié successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('categories.index')->with('error', 'Error lors de la modification de la categorie.'.$th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $categoryEmballage = Category::find($id);
            $categoryEmballage->delete();
            return redirect()->route('categories.index')->with('success', 'Categorie supprimé avec succès.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Impossible de supprimer cette categorie. '.$th->getMessage());
        }
    }
}

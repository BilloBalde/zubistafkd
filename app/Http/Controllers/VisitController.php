<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

use Illuminate\Http\Request;

class VisitController extends Controller
{
    /**
     * Page d'accueil vitrine (tous les visiteurs).
     */
    public function index()
    {
        return view('home', $this->buildHomePageData());
    }

    /**
     * Accueil catalogue pour les clients connectés (espace dédié).
     */
    public function shop()
    {
        return view('home', array_merge($this->buildHomePageData(), [
            'shopCustomerArea' => true,
        ]));
    }

    /**
     * Alias catalogue (route historique /products).
     */
    public function indexProducts()
    {
        return $this->index();
    }

    protected function buildHomePageData(): array
    {
        $mapProduct = function ($p) {
            $imageUrl = $p->image ? asset('products/' . $p->image) : null;

            return [
                'id' => $p->id,
                'name' => $p->libelle ?? $p->name,
                'category_name' => $p->category?->name ?? 'Sans catégorie',
                'price' => (float) $p->price,
                'old_price' => null,
                'discount' => null,
                'rating' => (float) ($p->rating ?? 4.5),
                'image' => $imageUrl,
            ];
        };

        $mapPromo = function ($p) {
            $imageUrl = $p->image ? asset('products/' . $p->image) : null;

            return [
                'id' => $p->id,
                'name' => $p->libelle ?? $p->name,
                'category_name' => $p->category?->name ?? 'Sans catégorie',
                'price' => (float) ($p->promo_price ?? $p->price),
                'old_price' => (float) $p->price,
                'discount' => $p->promo_price ? round((1 - $p->promo_price / $p->price) * 100) : 0,
                'rating' => (float) ($p->rating ?? 4.5),
                'image' => $imageUrl,
            ];
        };

        return [
            'categories' => Category::withCount('products')->get(),
            'bestProducts' => Product::with('category')
                ->orderByDesc('rating')
                ->take(8)
                ->get()
                ->map($mapProduct)
                ->values(),
            'promoProducts' => Product::with('category')
                ->whereNotNull('promo_price')
                ->get()
                ->map($mapPromo)
                ->values(),
        ];
    }

    public function loadMoreProducts(Request $request)
    {
        $offset = $request->input('offset', 0);
        $products = Product::orderBy('created_at', 'desc')->paginate(12);
        $html = view('visitor.partials.product_cards', compact('products'))->render();

        return response()->json([
            'html' => $html,
            'count' => $products->count(),
        ]);
    }

}






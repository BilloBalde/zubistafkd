<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Facture;
use App\Models\Logistic;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StoreProduct;
use App\Models\Purchase;
use App\Models\Store;
use App\Models\Sale;
use App\Models\Category;
use App\Models\Interet;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.check');
    }
    public function index()
    {
        if (auth()->user()->role_id == 3) {
            $userStoreId = auth()->user()->id;
            $store_id = Store::where('user_id', $userStoreId)->first()?->id;
            if (!$store_id) {
                return redirect('login')->with('error', 'Dites au manager de vous attribuer à une boutique afin de pouvoir continuer');
            }
           // Produits les plus vendus pour ce store
            // Produits les plus rentables (par chiffre d'affaires)
        $topProducts = Sale::where('store_id', $store_id)
            ->with('product')
            ->select(
                'product_id', 
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(prixTotal) as total_revenue')
            )
            ->groupBy('product_id')
            ->orderBy('total_revenue', 'DESC')
            ->take(10)
            ->get();
            $total_purchases = Purchase::where('store_id', $store_id)
                                ->selectRaw('SUM(price * quantity) as total')
                                ->value('total');
            $total_sales = Facture::where('store_id', $store_id)->sum('montant_total');
            $total_sales_paid = Facture::where('store_id', $store_id)
                                ->withSum('paiements as total_versement', 'versement')
                                ->get()
                                ->sum('total_versement');
            $total_expenses = Expense::where('store_id', $store_id)->sum('amount');
            $total_customers = Customer::all()->count();
            $total_quantities = StoreProduct::where('store_id', $store_id)->sum('quantity');
            $total_purchase_invoices = Logistic::where('store_id', $store_id)->count();
            $total_sales_invoices = Facture::where('store_id', $store_id)->count();
            $latestPurchases = Purchase::where('store_id', $store_id)->select('purchases.*')
            ->join(DB::raw('(SELECT MAX(id) as id FROM purchases GROUP BY product_id) as latest_purchases'), 'purchases.id', '=', 'latest_purchases.id')
            ->orderBy('purchases.created_at', 'desc')
            ->take(5)
            ->get();
            $latestSales = Sale::where('store_id', $store_id)->with('product')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            $salesData = DB::table('sales')
                ->where('store_id', $store_id)
                ->select(DB::raw('SUM(quantity) as total'), DB::raw('MONTH(created_at) as month'))
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $purchasesData = DB::table('purchases')
                ->where('store_id', $store_id)
                ->select(DB::raw('SUM(quantity) as total'), DB::raw('MONTH(created_at) as month'))
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();
            $gains = Store::where('id', $store_id)->value('balance');
            // Get data for each month
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $sales = [];
            $purchases = [];

            foreach (range(1, 12) as $month) {
                $sales[] = $salesData[$month] ?? 0;
                $purchases[] = -($purchasesData[$month] ?? 0); // Negate purchase values for the chart
            }
        }else{
            $store_id = 1;
            $total_purchases = Purchase::selectRaw('SUM(price * quantity) as total')
                                ->value('total');
             // Produits les plus vendus (tous stores)
          $topProducts = Sale::with('product')
            ->select(
                'product_id', 
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(prixTotal) as total_revenue')
            )
            ->groupBy('product_id')
            ->orderBy('total_revenue', 'DESC')
            ->take(10)
            ->get();
            $total_sales = Facture::all()->sum('montant_total');
            $total_sales_paid = Payment::all()->sum('versement');
            $total_expenses = Expense::all()->sum('amount');
            $total_customers = Customer::all()->count();
            $total_quantities = StoreProduct::all()->sum('quantity');
            $total_purchase_invoices = Logistic::all()->count();
            $total_sales_invoices = Facture::all()->count();
            $latestPurchases = Purchase::select('purchases.*')
            ->join(DB::raw('(SELECT MAX(id) as id FROM purchases GROUP BY product_id) as latest_purchases'), 'purchases.id', '=', 'latest_purchases.id')
            ->orderBy('purchases.created_at', 'desc')
            ->take(5)
            ->get();
            $latestSales = Sale::with('product')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            $salesData = DB::table('sales')
                ->select(DB::raw('SUM(quantity) as total'), DB::raw('MONTH(created_at) as month'))
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $purchasesData = DB::table('purchases')
                ->select(DB::raw('SUM(quantity) as total'), DB::raw('MONTH(created_at) as month'))
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();
                $gains = Store::sum('balance');
            // Get data for each month
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $sales = [];
            $purchases = [];

            foreach (range(1, 12) as $month) {
                $sales[] = $salesData[$month] ?? 0;
                $purchases[] = -($purchasesData[$month] ?? 0); // Negate purchase values for the chart
            }
        }

        return view('index', compact('latestPurchases', 'latestSales', 'total_purchases', 'total_sales', 'total_sales_paid', 'total_expenses', 'total_customers', 'total_quantities', 'total_purchase_invoices', 'total_sales_invoices', 'gains', 'sales', 'purchases', 'months','topProducts'));
    }

    
    

    
}

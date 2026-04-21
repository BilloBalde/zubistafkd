<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ecommerce\OrderRequest;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Notifications\OrderPlacedNotification;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $orderService;
    protected $paymentService;

    public function __construct(OrderService $orderService, PaymentService $paymentService)
    {
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }

    public function checkout()
    {
        $user = Auth::user();
        $addresses = $user->addresses;
        
        // Simuler la récupération du panier (Session ou DB)
        $cartItems = session()->get('cart', []);
        
        if (empty($cartItems)) {
            return redirect()->route('shop.home')->with('error', 'Votre panier est vide.');
        }

        $products = Product::whereIn('id', array_column($cartItems, 'product_id'))->get();
        $total = 0;
        foreach($products as $product) {
            foreach($cartItems as $item) {
                if ($item['product_id'] == $product->id) {
                    $total += $product->price * $item['quantity'];
                }
            }
        }

        return view('ecommerce.checkout', compact('addresses', 'cartItems', 'products', 'total'));
    }

    public function store(OrderRequest $request)
    {
        $order = $this->orderService->createOrder(
            Auth::id(),
            $request->delivery_address_id,
            $request->items,
            $request->payment_method
        );

        $screenshotPath = null;
        if ($request->payment_method === 'orange_money' && $request->hasFile('payment_screenshot')) {
            $file = $request->file('payment_screenshot');
            $filename = 'screenshot_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $screenshotPath = $file->storeAs('screenshots', $filename, 'public');

 
        }

        // Notification
        Auth::user()->notify(new OrderPlacedNotification($order));

        // Vider le panier
        session()->forget('cart');

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Votre commande a été prise en charge avec succès.');
    }

    public function index()
    {
        $orders = Auth::user()->orders()->with('items.product')->latest()->get();
        return view('ecommerce.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Auth::user()->orders()->with('items.product', 'address', 'facture.paiements')->findOrFail($id);
        return view('ecommerce.orders.show', compact('order'));
    }
}

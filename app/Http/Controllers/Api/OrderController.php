<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Notifications\OrderPlacedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected $orderService;
    protected $paymentService;

    public function __construct(OrderService $orderService, PaymentService $paymentService)
    {
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'delivery_address_id' => 'required|exists:delivery_addresses,id',
            'payment_method' => 'required|in:cod,orange_money',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $order = $this->orderService->createOrder(
            $request->user()->id,
            $request->delivery_address_id,
            $request->items,
            $request->payment_method
        );

        $response = [
            'message' => 'Votre commande a été prise en charge avec succès',
            'order' => $order
        ];

        if ($request->payment_method === 'orange_money') {
            $payment = $this->paymentService->initializeOrangeMoney($order);
            $response['payment'] = $payment;
        }

        // Notification
        $request->user()->notify(new OrderPlacedNotification($order));

        return response()->json($response, 201);
    }

    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items.product', 'address')->latest()->get();
        return response()->json($orders);
    }

    public function webhookOrangeMoney(Request $request)
    {
        $success = $this->paymentService->handleWebhook($request->all());

        if ($success) {
            return response()->json(['message' => 'Webhook processed']);
        }

        return response()->json(['message' => 'Failed to process webhook'], 400);
    }
}

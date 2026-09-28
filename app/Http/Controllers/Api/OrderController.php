<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Customer;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller {
    protected OrderService $orderService;

    public function __construct(OrderService $orderService) {
        $this->orderService = $orderService;
    }

    // Render the frontend billing view
    public function indexView() {
        $products = Product::all();
        $lowStockProducts = Product::where('stock_on_hand', '<=', 5)->get();
        
        return view('order', compact('products', 'lowStockProducts'));
    }

    // 1. Create Order Endpoint
    public function store(StoreOrderRequest $request): JsonResponse {
        try {
            $order = $this->orderService->createOrder($request->validated());
            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Order creation failed',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    // 2. Fetch Customer Order History Endpoint
    public function history(Request $request): JsonResponse {
        $request->validate(['email' => ['required', 'email']]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer) {
            return response()->json(['message' => 'Customer not found', 'data' => []], 404);
        }

        $orders = $customer->orders()->with('items.product')->latest()->get();

        return response()->json([
            'customer' => $customer,
            'orders' => $orders
        ]);
    }

    // 3. Low Stock Products Endpoint
    public function lowStock(Request $request): JsonResponse {
        $threshold = $request->query('threshold', 5);

        $products = Product::where('stock_on_hand', '<=', $threshold)->get();

        return response()->json([
            'threshold' => $threshold,
            'products' => $products
        ]);
    }
}
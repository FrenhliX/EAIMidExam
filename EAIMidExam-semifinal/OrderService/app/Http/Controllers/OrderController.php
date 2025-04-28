<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    private $userServiceUrl = 'http://localhost:8001';
    private $productServiceUrl = 'http://localhost:8003';

    private function verifyUser($userId)
    {
        try {
            \Log::info('Cek user ke: ' . $this->userServiceUrl . '/api/users/' . $userId);
            $response = Http::get($this->userServiceUrl . '/api/users/' . $userId);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function verifyProduct($productId)
    {
        try {
            $response = Http::get($this->productServiceUrl . '/api/products/' . $productId);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getProducts()
    {
        try {
            $response = Http::get($this->productServiceUrl . '/api/products');
            if ($response->successful()) {
                return response()->json($response->json());
            }
            return response()->json(['error' => 'Failed to fetch products'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        $orders = Order::with('items')->get();
        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items')->find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        return response()->json($order);
    }

    public function store(Request $request)
    {
        // Verify user exists in UserService
        if (!$this->verifyUser($request->user_id)) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Verify all products exist in ProductService
        foreach ($request->items as $item) {
            if (!$this->verifyProduct($item['product_id'])) {
                return response()->json(['error' => 'Product not found: ' . $item['product_id']], 404);
            }
        }

        $validated = $request->validate([
            'user_id' => 'required|integer',
            'total_amount' => 'required|numeric|min:0',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string|in:credit_card,bank_transfer,cash',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        $order = Order::create($validated);

        foreach ($validated['items'] as $item) {
            $order->items()->create($item);
        }

        return redirect()->back()->with('success', 'Order created successfully');
    }
    
    public function edit($id)
    {
        $order = Order::with('items')->findOrFail($id);

        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'sometimes|string|in:pending,processing,completed,cancelled',
            'payment_status' => 'sometimes|string|in:pending,paid,failed',
            'shipping_address' => 'sometimes|string'
        ]);

        $order->update($validated);
        return redirect()->route('orders.index')->with('success', 'Order updated successfully');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->items()->delete();
        $order->delete();
        return response()->json(null, 204);
    }

    public function getUserOrders($userId)
    {
        $orders = Order::with('items')
            ->where('user_id', $userId)
            ->get();
        return response()->json($orders);
    }

    public function getUsers()
    {
        try {
            $response = Http::get($this->userServiceUrl . '/api/users');
            if ($response->successful()) {
                return response()->json($response->json());
            }
            return response()->json(['error' => 'Failed to fetch users'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
} 
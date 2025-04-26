<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    private $userServiceUrl = 'http://localhost:8001';
    private $productServiceUrl = 'http://localhost:8002';

    public function index()
    {
        $orders = Order::with('items')->get();
        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with('items')->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'status' => 'required|string|in:pending,processing,completed,cancelled',
            'total_amount' => 'required|numeric|min:0',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|string|in:credit_card,bank_transfer,cash',
            'payment_status' => 'required|string|in:pending,paid,failed',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        $order = Order::create($validated);

        foreach ($validated['items'] as $item) {
            $order->items()->create($item);
        }

        return response()->json($order->load('items'), 201);
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
        return response()->json($order->load('items'));
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
        $orders = Order::with('orderItems')
            ->where('user_id', $userId)
            ->get();
        return response()->json($orders);
    }
} 
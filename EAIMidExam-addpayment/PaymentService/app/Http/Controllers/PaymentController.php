<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    private $userServiceUrl = 'http://localhost:8001';
    private $orderServiceUrl = 'http://localhost:8003';

    private function verifyUser($userId)
    {
        try {
            $response = Http::get($this->userServiceUrl . '/api/users/' . $userId);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function verifyOrder($orderId)
    {
        try {
            $response = Http::get($this->orderServiceUrl . '/api/orders/' . $orderId);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function index()
    {
        $payments = Payment::all();
        return response()->json($payments);
    }

    public function show($id)
    {
        $payment = Payment::findOrFail($id);
        return response()->json($payment);
    }

    public function store(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'order_id' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:credit_card,bank_transfer,cash_on_delivery'
        ]);

        // Verify user
        if (!$this->verifyUser($validated['user_id'])) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Verify order
        if (!$this->verifyOrder($validated['order_id'])) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Create payment
        $payment = Payment::create([
            'user_id' => $validated['user_id'],
            'order_id' => $validated['order_id'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'status' => 'pending' // default
        ]);

        return response()->json($payment, 201);
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:pending,paid,failed'
        ]);

        $payment->update([
            'status' => $validated['status']
        ]);

        return response()->json($payment);
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return response()->json(null, 204);
    }

        public function webIndex()
    {
        $payments = Payment::all();
        return view('payments.index', compact('payments'));
    }
    
    // Form create payment
    public function webCreate()
    {
        try {
            // Get users from UserService
            $usersResponse = Http::get($this->userServiceUrl . '/api/users');
            if (!$usersResponse->successful()) {
                \Log::error('Failed to fetch users', [
                    'status' => $usersResponse->status(),
                    'response' => $usersResponse->body()
                ]);
                return redirect()->back()->with('error', 'Failed to fetch users from UserService');
            }

            $users = $usersResponse->json() ?? [];
            if (isset($users['data'])) {
                $users = $users['data'];
            }

            return view('payments.create', compact('users'));
        } catch (\Exception $e) {
            \Log::error('Payment creation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error connecting to services: ' . $e->getMessage());
        }
    }
    
    // Simpan payment
    public function webStore(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'order_id' => 'required|integer',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);
    
        try {
            // Verify user
            if (!$this->verifyUser($request->user_id)) {
                return redirect()->back()->with('error', 'User not found');
            }
        
            // Verify and get order
            $orderResponse = Http::get($this->orderServiceUrl . '/api/orders/' . $request->order_id);
            if (!$orderResponse->successful()) {
                return redirect()->back()->with('error', 'Order not found');
            }
        
            $order = $orderResponse->json();
            
            // Add debug logging
            \Log::info('Creating payment with data:', [
                'user_id' => $request->user_id,
                'order_id' => $request->order_id,
                'amount' => $order['total_amount'],
                'payment_method' => $request->payment_method,
            ]);
        
            // Create payment
            $payment = Payment::create([
                'user_id' => $request->user_id,
                'order_id' => $request->order_id,
                'amount' => $request->amount,
                'payment_method' => strtolower(str_replace(' ', '_', $request->payment_method)),
                'status' => 'pending'
            ]);
        
            if (!$payment) {
                throw new \Exception('Failed to create payment record');
            }
        
            return redirect()->route('payments.index')->with('success', 'Payment created successfully');
        } catch (\Exception $e) {
            \Log::error('Payment creation error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error creating payment: ' . $e->getMessage());
        }
    }
    
    // Detail payment
    public function webShow($id)
    {
        $payment = Payment::findOrFail($id);
        return view('payments.show', compact('payment'));
    }
    
    // Form edit payment status
    public function webEdit($id)
    {
        $payment = Payment::findOrFail($id);
        return view('payments.edit', compact('payment'));
    }
    
    // Update status payment
    public function webUpdate(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,paid,failed'
        ]);
    
        $payment = Payment::findOrFail($id);
        $payment->update(['status' => $request->status]);
    
        return redirect()->route('payments.index')->with('success', 'Payment status updated successfully');
    }
    
    // Hapus payment
    public function webDestroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
    
        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully');
    }

}

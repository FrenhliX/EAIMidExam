<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    private $orderServiceUrl = 'http://localhost:8003';

    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'string', 'in:admin,customer'],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return redirect()->back()->with('success', 'User created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['nullable', 'string', 'in:admin,customer'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->update($request->except('password'));

        return redirect('/')->with('success', 'User updated successfully!');
    }


    public function destroy($id)
    {   
        $user = User::find($id); 
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted sucessfully!');
    }

    // public function getUserOrders($userId)
    // {
    //     try {
    //         $response = Http::get("http://localhost:8003/api/orders/user/{$userId}");
            
    //         if ($response->successful()) {
    //             return response()->json($response->json());
    //         } else {
    //             return response()->json(['error' => 'Failed to fetch orders from OrderService'], 500);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'OrderService is not available'], 503);
    //     }
    // }

    public function apiIndex()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function apiShow($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }
} 
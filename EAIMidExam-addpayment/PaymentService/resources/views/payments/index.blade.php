@extends('layouts.app')

@section('title', 'Payments List')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Payments List</h5>
            <a href="{{ route('payments.create') }}" class="btn btn-primary">Create Payment</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User ID</th>
                            <th>Order ID</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>{{ $payment->user_id }}</td>
                                <td>{{ $payment->order_id }}</td>
                                <td>Rp {{ number_format($payment->amount, 2) }}</td>
                                <td><span class="badge bg-info">{{ strtoupper(str_replace('_', ' ', $payment->payment_method)) }}</span></td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'pending' => 'bg-warning',
                                            'paid' => 'bg-success',
                                            'failed' => 'bg-danger'
                                        ][$payment->status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ strtoupper($payment->status) }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-sm btn-info">View</a>
                                        <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this payment?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
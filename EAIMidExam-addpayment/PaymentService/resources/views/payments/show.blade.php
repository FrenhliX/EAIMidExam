@extends('layouts.app')

@section('title', 'Payment Detail')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Payment Detail</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th width="150">Payment ID</th>
                            <td>{{ $payment->id }}</td>
                        </tr>
                        <tr>
                            <th>User ID</th>
                            <td>{{ $payment->user_id }}</td>
                        </tr>
                        <tr>
                            <th>Order ID</th>
                            <td>{{ $payment->order_id }}</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td>Rp {{ number_format($payment->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td><span class="badge bg-info">{{ strtoupper(str_replace('_', ' ', $payment->payment_method)) }}</span></td>
                        </tr>
                        <tr>
                            <th>Status</th>
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
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <a href="{{ route('payments.index') }}" class="btn btn-secondary">Back to List</a>
            <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-warning">Edit Payment</a>
        </div>
    </div>
@endsection
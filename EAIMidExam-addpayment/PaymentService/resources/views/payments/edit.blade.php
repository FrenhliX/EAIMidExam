@extends('layouts.app')

@section('title', 'Edit Payment')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Payment Status</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('payments.update', $payment->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Payment ID</label>
                    <input type="text" class="form-control" value="{{ $payment->id }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">Current Status</label>
                    <input type="text" class="form-control" value="{{ strtoupper($payment->status) }}" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">New Status</label>
                    <select name="status" class="form-select">
                        <option value="pending" {{ $payment->status == 'pending' ? 'selected' : '' }}>PENDING</option>
                        <option value="paid" {{ $payment->status == 'paid' ? 'selected' : '' }}>PAID</option>
                        <option value="failed" {{ $payment->status == 'failed' ? 'selected' : '' }}>FAILED</option>
                    </select>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Status</button>
                    <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
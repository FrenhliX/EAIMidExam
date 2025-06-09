<!DOCTYPE html>
<html>
<head>
    <title>Create Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
@extends('layouts.app')

@section('title', 'Create Payment')
@section('content')
    <div class="container mt-5">
        <h2>Create New Payment</h2>

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('payments.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Select User</label>
                <select name="user_id" id="user_id" class="form-control" required>
                    <option value="">Select User</option>
                    @if(isset($users) && is_array($users))
                        @foreach($users as $user)
                            <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mb-3">
                <label>Select Order</label>
                <select name="order_id" id="order_id" class="form-control" required>
                    <option value="">Select Order</option>
                </select>
            </div>

            <div id="orderDetails" class="mb-3 d-none">
                <h4>Order Details</h4>
                <div class="card">
                    <div class="card-body">
                        <div id="orderItems"></div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Shipping Address:</strong> <span id="shippingAddress"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Total Amount:</strong> Rp <span id="totalAmount"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label>Amount</label>
                <input type="number" name="amount" id="amount" class="form-control" readonly required>
            </div>

            <div class="mb-3">
                <label>Payment Method</label>
                <input type="text" name="payment_method" id="payment_method" class="form-control" readonly required>
                <input type="hidden" name="payment_method_original" id="payment_method_original">
            </div>

            <button type="submit" class="btn btn-primary">Create Payment</button>
            <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            // When user changes
            $('#user_id').change(function() {
                const userId = $(this).val();
                if (userId) {
                    // Fetch user's orders
                    $.get(`http://localhost:8003/api/orders/user/${userId}`, function(orders) {
                        const orderSelect = $('#order_id');
                        orderSelect.empty().append('<option value="">Select Order</option>');
                        orders.forEach(order => {
                            orderSelect.append(`<option value="${order.id}" 
                                data-amount="${order.total_amount}"
                                data-address="${order.shipping_address}"
                                data-payment="${order.payment_method}">
                                Order #${order.id} - Rp ${order.total_amount}
                            </option>`);
                        });
                    });
                }
            });

            // When order changes
            $('#order_id').change(function() {
            const selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                const orderId = selectedOption.val();

                // Get order details from OrderService
                $.get(`http://localhost:8003/api/orders/${orderId}`, function(order) {
                    $('#orderDetails').removeClass('d-none');

                        // Display order items
                        let itemsHtml = '<table class="table">';
                        itemsHtml += '<thead><tr><th>Product</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr></thead><tbody>';

                        order.items.forEach(item => {
                            itemsHtml += `
                                <tr>
                                    <td>Product #${item.product_id}</td>
                                    <td>${item.quantity}</td>
                                    <td>Rp ${item.price}</td>
                                    <td>Rp ${item.quantity * item.price}</td>
                                </tr>
                            `;
                        });
                        itemsHtml += '</tbody></table>';
                        $('#orderItems').html(itemsHtml);

                        // Set shipping address
                        $('#shippingAddress').text(order.shipping_address);

                        // Set total amount
                        $('#totalAmount').text(order.total_amount);
                        $('#amount').val(order.total_amount);

                        // Set payment method from order
                        $('#payment_method').val(order.payment_method.replace(/_/g, ' ').toUpperCase());
                        
                        const paymentMethod = order.payment_method;
                        $('#payment_method').val(paymentMethod.replace(/_/g, ' ').toUpperCase());
                        $('#payment_method_original').val(paymentMethod);
                    });
                } else {
                    $('#orderDetails').addClass('d-none');
                    $('#amount').val('');
                    $('#payment_method').val('');
                    $('#payment_method_original').val('');
                }
            });
        });
    </script>
@endsection
</body>
</html>
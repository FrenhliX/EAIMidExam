<!DOCTYPE html>
<html>
<head>
    <title>Edit Order - OrderService</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Salin saja style dari create page */
        body {
            background-color: #f0f2f5;
        }
        .container {
            padding-bottom: 2rem;
        }
        .card {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: none;
            margin-bottom: 1.5rem;
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem;
        }
        .card-body {
            background-color: #ffffff;
            padding: 1.5rem;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .form-label {
            color: #4a5568;
            font-weight: 500;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Edit Order #{{ $order->id }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h4>Order Details</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('orders.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">User</label>
                    <select class="form-control" name="user_id" id="userSelect" required>
                        <option value="">Select User</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Shipping Address</label>
                    <textarea class="form-control" name="shipping_address" required>{{ old('shipping_address', $order->shipping_address) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Payment Method</label>
                    <select class="form-control" name="payment_method" required>
                        <option value="credit_card" {{ $order->payment_method == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                        <option value="bank_transfer" {{ $order->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cash" {{ $order->payment_method == 'cash' ? 'selected' : '' }}>Cash on Delivery</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-control" name="status" required>
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Untuk Order Items, tampilkan daftar item -->
                <div class="mb-3">
                    <label class="form-label">Order Items</label>
                    <div id="orderItems">
                        @foreach ($order->items as $index => $item)
                        <div class="row mb-2 order-item">
                            <div class="col-md-6">
                                <select class="form-control product-select" name="items[{{ $index }}][product_id]" required>
                                    <option value="">Select Product</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control quantity-input" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="1" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control subtotal-display" readonly>
                                <input type="hidden" class="price-input" name="items[{{ $index }}][price]" value="{{ $item->price }}">
                                <input type="hidden" class="subtotal-input" name="items[{{ $index }}][subtotal]" value="{{ $item->quantity * $item->price }}">
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addOrderItem()">Add Another Item</button>
                </div>

                <div class="row mb-3">
                    <div class="col-md-8 text-end">
                        <strong>Total Amount:</strong>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="totalAmount" readonly value="Rp {{ number_format($order->total_amount, 0, ',', '.') }}">
                        <input type="hidden" name="total_amount" id="totalAmountInput" value="{{ $order->total_amount }}">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Order</button>
            </form>
        </div>
    </div>
</div>

<script>
    let itemCount = {{ count($order->items) }};
    let products = [];

    $(document).ready(function() {
        loadProducts();
        loadUsers();
        setupEventListeners();
    });

    function loadProducts() {
        $.get('{{ route('products.get') }}', function(response) {
            if (response.error) {
                alert('Failed to load products: ' + response.error);
                return;
            }
            products = response;
            updateProductSelects();
            setSelectedProducts();
        });
    }

    function loadUsers() {
        $.get('{{ route('users.get') }}', function(response) {
            const userSelect = $('#userSelect');
            userSelect.empty().append('<option value="">Select User</option>');
            const users = response.data || response;
            users.forEach(user => {
                userSelect.append(`<option value="${user.id}">${user.name} (${user.email})</option>`);
            });
            userSelect.val('{{ $order->user_id }}');
        });
    }

    function updateProductSelects() {
        $('.product-select').each(function() {
            const select = $(this);
            if (select.find('option').length <= 1) {
                products.forEach(product => {
                    select.append(`<option value="${product.id}" data-price="${product.price}" data-stock="${product.stock}">${product.name} - Rp ${product.price.toLocaleString()} (Stock: ${product.stock})</option>`);
                });
            }
        });
    }

    function setSelectedProducts() {
        @foreach ($order->items as $index => $item)
            $(`select[name="items[{{ $index }}][product_id]"]`).val('{{ $item->product_id }}').trigger('change');
        @endforeach
    }

    function setupEventListeners() {
        $(document).on('change', '.product-select', function() {
            const selectedOption = $(this).find('option:selected');
            const price = selectedOption.data('price');
            const stock = selectedOption.data('stock');
            const quantityInput = $(this).closest('.row').find('.quantity-input');
            
            $(this).closest('.row').find('.price-input').val(price);
            quantityInput.attr('max', stock);

            calculateSubtotal($(this).closest('.row'));
        });

        $(document).on('input', '.quantity-input', function() {
            calculateSubtotal($(this).closest('.row'));
        });
    }

    function calculateSubtotal(row) {
        const quantity = parseInt(row.find('.quantity-input').val()) || 0;
        const price = parseFloat(row.find('.price-input').val()) || 0;
        const subtotal = quantity * price;

        row.find('.subtotal-display').val('Rp ' + subtotal.toLocaleString());
        row.find('.subtotal-input').val(subtotal);

        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        $('.subtotal-input').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        
        $('#totalAmount').val('Rp ' + total.toLocaleString());
        $('#totalAmountInput').val(total);
    }

    function addOrderItem() {
        const html = `
            <div class="row mb-2 order-item">
                <div class="col-md-6">
                    <select class="form-control product-select" name="items[${itemCount}][product_id]" required>
                        <option value="">Select Product</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control quantity-input" name="items[${itemCount}][quantity]" value="1" min="1" required>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control subtotal-display" readonly>
                    <input type="hidden" class="price-input" name="items[${itemCount}][price]">
                    <input type="hidden" class="subtotal-input" name="items[${itemCount}][subtotal]">
                </div>
            </div>
        `;
        $('#orderItems').append(html);
        updateProductSelects();
        itemCount++;
    }
</script>
</body>
</html>

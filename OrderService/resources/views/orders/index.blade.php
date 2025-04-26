<!DOCTYPE html>
<html>
<head>
    <title>Order Management - OrderService</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Order Management</h2>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Create Order Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Create New Order</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">User</label>
                        <select class="form-control" name="user_id" id="userSelect" required>
                            <option value="">Select User</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shipping Address</label>
                        <textarea class="form-control" name="shipping_address" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-control" name="payment_method" required>
                            <option value="credit_card">Credit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="mb-3">
                        <label class="form-label">Order Items</label>
                        <div id="orderItems">
                            <div class="row mb-2 order-item">
                                <div class="col-md-6">
                                    <select class="form-control product-select" name="items[0][product_id]" required>
                                        <option value="">Select Product</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" class="form-control quantity-input" name="items[0][quantity]" placeholder="Qty" required min="1" value="1">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control subtotal-display" readonly>
                                    <input type="hidden" class="price-input" name="items[0][price]">
                                    <input type="hidden" class="subtotal-input" name="items[0][subtotal]">
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addOrderItem()">Add Another Item</button>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-8 text-end">
                            <strong>Total Amount:</strong>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="totalAmount" readonly>
                            <input type="hidden" name="total_amount" id="totalAmountInput">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Order</button>
                </form>
            </div>
        </div>

        <!-- Orders List -->
        <div class="card">
            <div class="card-header">
                <h4>Orders List</h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User ID</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                            <th>Payment Method</th>
                            <th>Payment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>{{ $order->user_id }}</td>
                            <td>
                                <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'processing' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($order->payment_method) }}</td>
                            <td>
                                <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewOrderDetails({{ $order->id }})">View Details</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Order details will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        let itemCount = 1;
        let products = [];

        // Load products when page loads
        $(document).ready(function() {
            loadProducts();
            loadUsers();
            setupEventListeners();
        });

        // Load products from ProductService
        function loadProducts() {
            $.get('{{ route('products.get') }}', function(response) {
                if (response.error) {
                    alert('Failed to load products: ' + response.error);
                    return;
                }
                products = response;
                updateProductSelects();
            }).fail(function() {
                alert('Failed to load products from ProductService');
            });
        }

        // Load users from UserService
        function loadUsers() {
            $.get('{{ route('users.get') }}', function(response) {
                if (response.error) {
                    alert('Failed to load users: ' + response.error);
                    return;
                }
                const userSelect = $('#userSelect');
                userSelect.empty().append('<option value="">Select User</option>');
                const users = response.data || response;
                users.forEach(user => {
                    userSelect.append(`<option value="${user.id}">${user.name} (${user.email})</option>`);
                });
            }).fail(function() {
                alert('Failed to load users from UserService');
            });
        }

        // Update all product select dropdowns
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

        // Setup event listeners for product selection and quantity changes
        function setupEventListeners() {
            $(document).on('change', '.product-select', function() {
                const selectedOption = $(this).find('option:selected');
                const price = selectedOption.data('price');
                const stock = selectedOption.data('stock');
                const quantityInput = $(this).closest('.row').find('.quantity-input');
                
                // Update price input
                $(this).closest('.row').find('.price-input').val(price);
                
                // Update max quantity based on stock
                quantityInput.attr('max', stock);
                
                // Calculate subtotal
                calculateSubtotal($(this).closest('.row'));
            });

            $(document).on('input', '.quantity-input', function() {
                calculateSubtotal($(this).closest('.row'));
            });
        }

        // Calculate subtotal for a row
        function calculateSubtotal(row) {
            const quantity = parseInt(row.find('.quantity-input').val()) || 0;
            const price = parseFloat(row.find('.price-input').val()) || 0;
            const subtotal = quantity * price;
            
            row.find('.subtotal-display').val('Rp ' + subtotal.toLocaleString());
            row.find('.subtotal-input').val(subtotal);
            
            calculateTotal();
        }

        // Calculate total amount
        function calculateTotal() {
            let total = 0;
            $('.subtotal-input').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            
            $('#totalAmount').val('Rp ' + total.toLocaleString());
            $('#totalAmountInput').val(total);
        }

        // Add new order item row
        function addOrderItem() {
            const html = `
                <div class="row mb-2 order-item">
                    <div class="col-md-6">
                        <select class="form-control product-select" name="items[${itemCount}][product_id]" required>
                            <option value="">Select Product</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control quantity-input" name="items[${itemCount}][quantity]" placeholder="Qty" required min="1" value="1">
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

        // View order details
        function viewOrderDetails(orderId) {
            $.get(`/orders/${orderId}`, function(response) {
                $('#orderDetailsContent').html(response);
                $('#orderDetailsModal').modal('show');
            });
        }
    </script>
</body>
</html> 
<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
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
        .btn-warning {
            background-color: #ffc107;
            border: none;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-info {
            background-color: #17a2b8;
            border: none;
            color: white;
        }
        .btn-info:hover {
            background-color: #138496;
            color: white;
        }
        .table {
            background-color: #ffffff;
        }
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        .modal-content {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2d3748;
            margin-bottom: 1.5rem;
        }
        .form-label {
            color: #4a5568;
            font-weight: 500;
        }
        .form-control {
            border: 1px solid #e2e8f0;
            padding: 0.5rem 0.75rem;
        }
        .form-control:focus {
            border-color: #3182ce;
            box-shadow: 0 0 0 2px rgba(49,130,206,0.1);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>User Management</h2>
        
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

        <!-- Create User Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h4>Create New User</h4>
            </div>
            <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-control" name="role" required>
                            <option value="customer">Customer</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="phone" class="form-control" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="address" class="form-control" name="address" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </form>
            </div>
        </div>

        <!-- Users List -->
        <div class="card">
            <div class="card-header">
                <h4>Users List</h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>{{ $user->address }}</td>
                            <td>{{ $user->role }}</td>
                            <td>
                                <button class="btn btn-sm btn-info view-orders" data-user-id="{{ $user->id }}" data-bs-toggle="modal" data-bs-target="#ordersModal">View Orders</button>

                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>

                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Orders Modal -->
    <div class="modal fade" id="ordersModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Orders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="ordersList">
                        <!-- Orders will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.view-orders').click(function() {
                const userId = $(this).data('user-id');
                
                // Fetch orders from OrderService API
                $.get(`/api/users/${userId}/orders`, function(response) {
                    if (response.length === 0) {
                        $('#ordersList').html('<div class="alert alert-info">No orders found for this user.</div>');
                        return;
                    }

                    let html = '<table class="table">';
                    html += '<thead><tr><th>Order ID</th><th>Total Amount</th><th>Payment Method</th><th>Status</th><th>Items</th></tr></thead>';
                    html += '<tbody>';
                    
                    response.forEach(order => {
                        html += `
                            <tr>
                                <td>${order.id}</td>
                                <td>Rp ${order.total_amount.toLocaleString()}</td>
                                <td>${order.payment_method}</td>
                                <td>${order.status}</td>
                                <td>${order.items ? order.items.length : 0} items</td>
                            </tr>
                        `;
                    });
                    
                    html += '</tbody></table>';
                    $('#ordersList').html(html);
                }).fail(function(error) {
                    $('#ordersList').html('<div class="alert alert-danger">Failed to load orders: ' + (error.responseJSON?.error || 'Unknown error') + '</div>');
                });
            });
        });
    </script>
</body>
</html> 
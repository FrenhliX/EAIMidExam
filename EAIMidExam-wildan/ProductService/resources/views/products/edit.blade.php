<!DOCTYPE html>
<html>
<head>
    <title>Edit Product - ProductService</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Product</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $product->name) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" class="form-control" name="category" value="{{ old('category', $product->category) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control" name="description" rows="3" required>{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Price (Rp)</label>
                    <input type="number" class="form-control" name="price" value="{{ old('price', $product->price) }}" min="0" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stock</label>
                    <input type="number" class="form-control" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Admin Name</label>
                    <input type="text" class="form-control" name="admin_name" value="{{ old('admin_name', $product->admin_name) }}" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="{{ url('/') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>

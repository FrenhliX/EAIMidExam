# E-Commerce Microservices System

Sistem e-commerce sederhana yang terdiri dari 3 microservice:
1. UserService (Port: 8001)
2. ProductService (Port: 8002)
3. OrderService (Port: 8003)

## API Documentation

### UserService (http://localhost:8001)

#### Endpoints:
- `GET /api/users` - Get all users
- `GET /api/users/{id}` - Get user by ID
- `POST /api/users` - Create new user
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user
- `GET /api/users/{id}/orders` - Get user's order history

### ProductService (http://localhost:8002)

#### Endpoints:
- `GET /api/products` - Get all products
- `GET /api/products/{id}` - Get product by ID
- `POST /api/products` - Create new product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product

### OrderService (http://localhost:8003)

#### Endpoints:
- `GET /api/orders` - Get all orders
- `GET /api/orders/{id}` - Get order by ID
- `POST /api/orders` - Create new order
- `PUT /api/orders/{id}` - Update order
- `DELETE /api/orders/{id}` - Delete order
- `GET /api/orders/user/{userId}` - Get user's orders

## Service Communication Flow

### Creating an Order
1. Client sends POST request to OrderService
2. OrderService validates user by calling UserService
3. OrderService validates products by calling ProductService
4. If validations pass, order is created
5. Order data is stored in OrderService

### Getting User's Order History
1. Client sends GET request to UserService
2. UserService calls OrderService to get order history
3. OrderService returns order data
4. UserService returns data to client

## Example API Calls

### Create User
```bash
curl -X POST http://localhost:8001/api/users \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "role": "customer"
  }'
```

### Create Product
```bash
curl -X POST http://localhost:8002/api/products \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Product 1",
    "description": "Description",
    "price": 100.00,
    "stock": 10,
    "category": "Category"
  }'
```

### Create Order
```bash
curl -X POST http://localhost:8003/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 1,
    "shipping_address": "123 Main St",
    "payment_method": "credit_card",
    "items": [
      {
        "product_id": 1,
        "quantity": 2,
        "price": 100.00
      }
    ]
  }'
```

## Setup Instructions

1. Clone the repository
2. Configure .env files for each service
3. Run migrations:
   ```bash
   cd UserService && php artisan migrate
   cd ProductService && php artisan migrate
   cd OrderService && php artisan migrate
   ```
4. Start the services:
   ```bash
   cd UserService && php artisan serve --port=8001
   cd ProductService && php artisan serve --port=8002
   cd OrderService && php artisan serve --port=8003
   ``` 
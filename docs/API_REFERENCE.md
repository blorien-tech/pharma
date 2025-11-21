# BLORIEN Pharma - API Reference

**Version:** 2.6.0
**Base URL:** `/api`
**Authentication:** Session-based (Laravel Sanctum compatible)
**Last Updated:** November 2025

---

## Table of Contents

1. [Overview](#overview)
2. [Authentication](#authentication)
3. [Response Format](#response-format)
4. [Error Handling](#error-handling)
5. [Products API](#products-api)
6. [Batches API](#batches-api)
7. [Transactions API](#transactions-api)
8. [Customers API](#customers-api)
9. [Dues API](#dues-api)
10. [Users API](#users-api)
11. [Dashboard API](#dashboard-api)
12. [Analytics API](#analytics-api)

---

## Overview

The BLORIEN Pharma API provides programmatic access to all system features. All API endpoints require authentication and return JSON responses.

### Base Information

- **Protocol:** HTTP/HTTPS
- **Format:** JSON
- **Authentication:** Session-based (cookies)
- **Charset:** UTF-8
- **Timezone:** Asia/Dhaka (UTC+6)

### Rate Limiting

- **Authenticated:** 60 requests/minute
- **Unauthenticated:** 10 requests/minute

---

## Authentication

### Session Authentication

All API requests require an authenticated session:

```javascript
// Include CSRF token in all POST/PUT/DELETE requests
headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    'Content-Type': 'application/json'
}
```

### Login

**Endpoint:** `POST /login`

**Request:**
```json
{
    "email": "user@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "role": "owner"
    }
}
```

### Logout

**Endpoint:** `POST /logout`

**Response:**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

---

## Response Format

### Success Response

```json
{
    "success": true,
    "message": "Operation successful",
    "data": {
        // Response data
    }
}
```

### Error Response

```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field_name": [
            "Validation error message"
        ]
    }
}
```

### HTTP Status Codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 201 | Created |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Server Error |

---

## Error Handling

### Validation Errors

```json
{
    "success": false,
    "message": "The given data was invalid.",
    "errors": {
        "name": ["The name field is required."],
        "email": ["The email has already been taken."]
    }
}
```

### Business Logic Errors

```json
{
    "success": false,
    "message": "Insufficient stock for Napa 500mg"
}
```

---

## Products API

### List Products

**Endpoint:** `GET /api/products`

**Query Parameters:**
- `search` (optional) - Search by name, generic, brand, SKU
- `active` (optional) - Filter by active status (1/0)
- `per_page` (optional) - Items per page (default: 15)

**Request:**
```http
GET /api/products?search=napa&active=1&per_page=20
```

**Response:**
```json
{
    "success": true,
    "products": [
        {
            "id": 1,
            "name": "Napa",
            "generic_name": "Paracetamol",
            "brand_name": "Napa",
            "sku": "NAPA-500",
            "barcode": "8941100501504",
            "purchase_price": "0.80",
            "selling_price": "1.50",
            "current_stock": 500,
            "min_stock": 50,
            "is_active": true,
            "supplier": {
                "id": 1,
                "name": "Beximco Pharma"
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 20,
        "total": 150,
        "last_page": 8
    }
}
```

### Get Product

**Endpoint:** `GET /api/products/{id}`

**Response:**
```json
{
    "success": true,
    "product": {
        "id": 1,
        "name": "Napa",
        "generic_name": "Paracetamol",
        "description": "Pain reliever and fever reducer",
        "sku": "NAPA-500",
        "current_stock": 500,
        "batches": [
            {
                "id": 1,
                "batch_number": "BATCH-001",
                "expiry_date": "2025-12-31",
                "quantity_remaining": 100,
                "status": "active"
            }
        ]
    }
}
```

### Create Product

**Endpoint:** `POST /api/products`

**Request:**
```json
{
    "name": "Napa",
    "generic_name": "Paracetamol",
    "brand_name": "Napa",
    "sku": "NAPA-500",
    "barcode": "8941100501504",
    "supplier_id": 1,
    "description": "Pain reliever",
    "purchase_price": 0.80,
    "selling_price": 1.50,
    "current_stock": 0,
    "min_stock": 50,
    "is_active": true
}
```

**Validation Rules:**
- `name`: required, max:255
- `sku`: required, unique, max:255
- `purchase_price`: required, numeric, min:0
- `selling_price`: required, numeric, min:0
- `min_stock`: required, integer, min:0

**Response:**
```json
{
    "success": true,
    "message": "Product created successfully",
    "product": {
        "id": 1,
        "name": "Napa",
        "sku": "NAPA-500",
        // ... full product data
    }
}
```

### Update Product

**Endpoint:** `PUT /api/products/{id}` or `POST /api/products/{id}`

**Request:** Same as create (all fields optional)

**Response:**
```json
{
    "success": true,
    "message": "Product updated successfully",
    "product": { /* updated product */ }
}
```

### Delete Product

**Endpoint:** `DELETE /api/products/{id}`

**Response:**
```json
{
    "success": true,
    "message": "Product deleted successfully"
}
```

### Search Products

**Endpoint:** `GET /api/products/search`

**Query Parameters:**
- `q` (required) - Search query

**Request:**
```http
GET /api/products/search?q=napa
```

**Response:**
```json
{
    "success": true,
    "products": [
        {
            "id": 1,
            "name": "Napa",
            "generic_name": "Paracetamol",
            "selling_price": "1.50",
            "current_stock": 500,
            "is_low_stock": false
        }
    ]
}
```

### Quick Stock Add

**Endpoint:** `POST /api/products/quick-stock`

**Request:**
```json
{
    "product_id": 1,
    "quantity": 100,
    "batch_number": "BATCH-123",
    "expiry_date": "2025-12-31",
    "purchase_price": 0.80
}
```

**Validation:**
- `product_id`: required, exists:products
- `quantity`: required, integer, min:1
- `batch_number`: required, max:255
- `expiry_date`: required, date, after:today
- `purchase_price`: optional, numeric, min:0

**Response:**
```json
{
    "success": true,
    "message": "Stock added successfully",
    "batch": {
        "id": 5,
        "batch_number": "BATCH-123",
        "quantity_received": 100,
        "quantity_remaining": 100
    }
}
```

---

## Batches API

### Create Batch

**Endpoint:** `POST /api/products/{product}/batches`

**Request:**
```json
{
    "batch_number": "BATCH-001",
    "expiry_date": "2025-12-31",
    "quantity_received": 100,
    "purchase_price": 0.80
}
```

**Response:**
```json
{
    "success": true,
    "message": "Batch created successfully",
    "batch": {
        "id": 1,
        "product_id": 1,
        "batch_number": "BATCH-001",
        "expiry_date": "2025-12-31",
        "quantity_received": 100,
        "quantity_remaining": 100,
        "purchase_price": "0.80"
    }
}
```

### Get Expiring Batches

**Endpoint:** `GET /api/batches/expiring`

**Query Parameters:**
- `days` (optional) - Days ahead (default: 30)

**Response:**
```json
{
    "success": true,
    "batches": [
        {
            "id": 1,
            "product": {
                "id": 1,
                "name": "Napa",
                "sku": "NAPA-500"
            },
            "batch_number": "BATCH-001",
            "expiry_date": "2025-01-15",
            "quantity_remaining": 50,
            "days_until_expiry": 25
        }
    ]
}
```

### Get Expired Batches

**Endpoint:** `GET /api/batches/expired`

**Response:**
```json
{
    "success": true,
    "batches": [
        {
            "id": 2,
            "product": {
                "id": 2,
                "name": "Sergel",
                "sku": "SERGEL-20"
            },
            "batch_number": "BATCH-002",
            "expiry_date": "2024-11-01",
            "quantity_remaining": 10,
            "days_expired": 50
        }
    ]
}
```

---

## Transactions API

### Complete Sale

**Endpoint:** `POST /api/transactions`

**Request:**
```json
{
    "items": [
        {
            "product_id": 1,
            "quantity": 2,
            "unit_price": 1.50
        },
        {
            "product_id": 2,
            "quantity": 1,
            "unit_price": 25.00
        }
    ],
    "customer_id": null,
    "payment_method": "CASH",
    "amount_paid": 100.00,
    "discount": 5.00,
    "notes": "Optional notes"
}
```

**Validation:**
- `items`: required, array, min:1
- `items.*.product_id`: required, exists:products
- `items.*.quantity`: required, integer, min:1
- `payment_method`: required, in:CASH,CARD,MOBILE,CREDIT,OTHER
- `amount_paid`: required_if:payment_method,CASH

**Response:**
```json
{
    "success": true,
    "message": "Sale completed successfully",
    "transaction": {
        "id": 1,
        "receipt_number": "INV-20250101-0001",
        "type": "SALE",
        "subtotal": "28.00",
        "discount": "5.00",
        "total": "23.00",
        "payment_method": "CASH",
        "amount_paid": "100.00",
        "change_amount": "77.00",
        "created_at": "2025-01-01 10:30:00",
        "items": [
            {
                "product": {
                    "id": 1,
                    "name": "Napa",
                    "sku": "NAPA-500"
                },
                "quantity": 2,
                "unit_price": "1.50",
                "subtotal": "3.00"
            },
            {
                "product": {
                    "id": 2,
                    "name": "Sergel",
                    "sku": "SERGEL-20"
                },
                "quantity": 1,
                "unit_price": "25.00",
                "subtotal": "25.00"
            }
        ]
    }
}
```

### Get Transaction

**Endpoint:** `GET /api/transactions/{id}`

**Response:**
```json
{
    "success": true,
    "transaction": {
        "id": 1,
        "receipt_number": "INV-20250101-0001",
        "type": "SALE",
        "total": "23.00",
        "payment_method": "CASH",
        "user": {
            "id": 1,
            "name": "John Doe"
        },
        "items": [ /* items array */ ]
    }
}
```

### Today's Transactions

**Endpoint:** `GET /api/transactions/today`

**Response:**
```json
{
    "success": true,
    "transactions": [ /* array of transactions */ ],
    "summary": {
        "total_sales": "1250.00",
        "total_transactions": 15,
        "cash_sales": "800.00",
        "card_sales": "250.00",
        "credit_sales": "200.00"
    }
}
```

### Recent Transactions

**Endpoint:** `GET /api/transactions/recent`

**Query Parameters:**
- `limit` (optional) - Number of transactions (default: 10)

**Response:**
```json
{
    "success": true,
    "transactions": [ /* recent transactions */ ]
}
```

### Process Return

**Endpoint:** `POST /api/transactions/{id}/return`

**Request:**
```json
{
    "items": [
        {
            "transaction_item_id": 1,
            "quantity": 1,
            "reason": "Defective"
        }
    ]
}
```

**Response:**
```json
{
    "success": true,
    "message": "Return processed successfully",
    "return_transaction": {
        "id": 2,
        "type": "RETURN",
        "return_for_transaction_id": 1,
        "total": "-1.50"
    }
}
```

---

## Customers API

*Note: Customer management is primarily done through web interface*

### List Customers

**Endpoint:** `GET /api/customers`

**Response:**
```json
{
    "success": true,
    "customers": [
        {
            "id": 1,
            "name": "Ahmed Khan",
            "phone": "+880 1712-345678",
            "credit_limit": "5000.00",
            "current_balance": "1250.00",
            "credit_enabled": true,
            "is_active": true
        }
    ]
}
```

---

## Dues API

### Create Due

**Endpoint:** `POST /api/dues`

**Request:**
```json
{
    "customer_name": "Ahmed Khan",
    "customer_phone": "+880 1712-345678",
    "amount": 500.00,
    "transaction_id": 1,
    "due_date": "2025-02-01",
    "notes": "Optional notes"
}
```

**Validation:**
- `customer_name`: required, max:255
- `customer_phone`: optional, max:20
- `amount`: required, numeric, min:0.01
- `transaction_id`: optional, exists:transactions
- `due_date`: optional, date, after_or_equal:today

**Response:**
```json
{
    "success": true,
    "message": "Due created successfully",
    "due": {
        "id": 1,
        "customer_name": "Ahmed Khan",
        "customer_phone": "+880 1712-345678",
        "amount": "500.00",
        "amount_paid": "0.00",
        "amount_remaining": "500.00",
        "status": "PENDING"
    }
}
```

### Phone Lookup

**Endpoint:** `GET /api/dues/lookup/phone`

**Query Parameters:**
- `phone` (required) - Phone number

**Response:**
```json
{
    "success": true,
    "dues": [
        {
            "id": 1,
            "customer_name": "Ahmed Khan",
            "amount": "500.00",
            "amount_remaining": "300.00",
            "status": "PARTIAL",
            "created_at": "2025-01-01"
        }
    ],
    "summary": {
        "total_dues": 2,
        "total_amount": "800.00",
        "total_paid": "200.00",
        "total_remaining": "600.00"
    }
}
```

### Dues Statistics

**Endpoint:** `GET /api/dues/statistics`

**Response:**
```json
{
    "success": true,
    "statistics": {
        "pending": {
            "count": 5,
            "amount": "2500.00"
        },
        "partial": {
            "count": 3,
            "amount": "1200.00"
        },
        "overdue": {
            "count": 2,
            "amount": "800.00"
        },
        "total": {
            "count": 10,
            "amount": "4500.00"
        }
    }
}
```

---

## Users API

### List Users

**Endpoint:** `GET /api/users` (Owner/Manager only)

**Response:**
```json
{
    "success": true,
    "users": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "owner",
            "is_active": true,
            "created_at": "2025-01-01"
        }
    ]
}
```

### Create User

**Endpoint:** `POST /api/users` (Owner/Manager only)

**Request:**
```json
{
    "name": "Jane Smith",
    "email": "jane@example.com",
    "password": "password123",
    "role": "cashier",
    "is_active": true
}
```

**Validation:**
- `name`: required, max:255
- `email`: required, email, unique
- `password`: required, min:8
- `role`: required, in:owner,manager,cashier

**Response:**
```json
{
    "success": true,
    "message": "User created successfully",
    "user": {
        "id": 2,
        "name": "Jane Smith",
        "email": "jane@example.com",
        "role": "cashier"
    }
}
```

---

## Dashboard API

### Dashboard Stats

**Endpoint:** `GET /api/dashboard/stats`

**Response:**
```json
{
    "success": true,
    "stats": {
        "today_sales": "1250.00",
        "today_transactions": 15,
        "low_stock_count": 5,
        "expiring_soon_count": 3,
        "expired_count": 1,
        "pending_dues": "2500.00",
        "overdue_dues": "800.00",
        "pending_purchase_orders": 2
    }
}
```

---

## Analytics API

### Sales Data

**Endpoint:** `GET /api/analytics/sales`

**Query Parameters:**
- `period` (optional) - Period: week/month/year (default: month)

**Response:**
```json
{
    "success": true,
    "data": {
        "labels": ["Jan 1", "Jan 2", "Jan 3", /* ... */],
        "sales": [1200, 1500, 980, /* ... */],
        "transactions": [12, 15, 10, /* ... */]
    },
    "summary": {
        "total_sales": "45000.00",
        "total_transactions": 450,
        "average_transaction": "100.00",
        "growth_percentage": 12.5
    }
}
```

---

## Daily Closing API

### Get Daily Data

**Endpoint:** `GET /api/daily-closing/data`

**Query Parameters:**
- `date` (optional) - Date in Y-m-d format (default: today)

**Response:**
```json
{
    "success": true,
    "data": {
        "date": "2025-01-01",
        "sales": {
            "cash": "5000.00",
            "card": "2000.00",
            "mobile": "1500.00",
            "credit": "500.00",
            "other": "0.00",
            "total": "9000.00"
        },
        "transactions": {
            "count": 45,
            "average": "200.00"
        },
        "dues": {
            "created": "1200.00",
            "payments": {
                "cash": "500.00",
                "card": "200.00",
                "mobile": "100.00",
                "total": "800.00"
            }
        },
        "cash_in_hand": "5500.00",
        "total_revenue": "9800.00"
    }
}
```

---

## Webhooks (Future Feature)

*Coming in Version 3.0*

### Events

- `transaction.completed`
- `product.low_stock`
- `batch.expiring_soon`
- `batch.expired`
- `due.overdue`
- `customer.credit_limit_exceeded`

---

## SDK Examples

### JavaScript/Axios

```javascript
// Configure Axios
const api = axios.create({
    baseURL: '/api',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json'
    }
});

// Complete a sale
async function completeSale(items, paymentMethod) {
    try {
        const response = await api.post('/transactions', {
            items: items,
            payment_method: paymentMethod
        });

        return response.data;
    } catch (error) {
        console.error('Sale failed:', error.response.data);
        throw error;
    }
}

// Search products
async function searchProducts(query) {
    const response = await api.get('/products/search', {
        params: { q: query }
    });

    return response.data.products;
}
```

### PHP/Guzzle

```php
<?php

use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'http://localhost:8000/api/',
    'cookies' => true, // Maintain session
]);

// Get dashboard stats
$response = $client->get('dashboard/stats');
$stats = json_decode($response->getBody(), true);

// Create product
$response = $client->post('products', [
    'json' => [
        'name' => 'Napa',
        'sku' => 'NAPA-500',
        'purchase_price' => 0.80,
        'selling_price' => 1.50,
        'min_stock' => 50
    ]
]);
```

---

## API Changelog

### Version 2.6.0 (Current)
- Added `quick-stock` endpoint
- Added `dues/lookup/phone` endpoint
- Added `dues/statistics` endpoint
- Added `daily-closing/data` endpoint
- Enhanced product search with generic/brand support

### Version 2.5.0
- Added analytics endpoints
- Added batch expiry endpoints
- Improved error messages

---

## Support

### API Issues
- GitHub Issues: [Report API bugs](https://github.com/blorien-tech/pharma/issues)
- Documentation: This file

### Feature Requests
- Create an issue with label `api-enhancement`

---

**Document Version:** 1.0
**API Version:** 2.6.0
**Last Updated:** November 2025

---

*End of API Reference*

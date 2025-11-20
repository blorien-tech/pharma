# BLORIEN PHARMA - QUICK REFERENCE GUIDE

## Database Tables at a Glance

| Table | Purpose | Key Columns | Relations | Soft Delete |
|-------|---------|------------|-----------|-------------|
| **users** | Auth & authorization | id, email, password, role, is_active | - | No |
| **products** | Product catalog | id, sku, name, purchase_price, selling_price, current_stock, min_stock | supplier, batches, items | Yes |
| **product_batches** | Batch tracking | id, product_id, batch_number, expiry_date, quantity_remaining | product, items | No |
| **transactions** | Sales/returns | id, type, user_id, customer_id, total, payment_method | user, items, customer, related | No |
| **transaction_items** | Line items | id, transaction_id, product_id, batch_id, quantity, unit_price | transaction, product, batch | No |
| **suppliers** | Vendor info | id, name, email, phone, company_name | products, orders | Yes |
| **purchase_orders** | PO workflow | id, po_number, supplier_id, status, total | supplier, items, user | Yes |
| **purchase_order_items** | PO line items | id, purchase_order_id, product_id, quantity_ordered, quantity_received | order, product | No |
| **customers** | Customer info | id, name, phone, email, credit_limit, current_balance, credit_enabled | transactions, credit_txns | Yes |
| **customer_credit_transactions** | Credit audit | id, customer_id, type, amount, balance_before, balance_after | customer, transaction, user | No |

---

## Key Models & Relationships

```
User
├── hasMany(Transaction)
├── hasMany(PurchaseOrder)
└── hasMany(CustomerCreditTransaction as creator)

Product
├── belongsTo(Supplier)
├── hasMany(ProductBatch)
├── hasMany(TransactionItem)
└── activeBatches() [scoped]

ProductBatch
├── belongsTo(Product)
└── hasMany(TransactionItem)

Transaction
├── belongsTo(User)
├── belongsTo(Customer)
├── belongsTo(Transaction as relatedTransaction)
├── hasMany(TransactionItem)
└── hasMany(Transaction as returns)

TransactionItem
├── belongsTo(Transaction)
├── belongsTo(Product)
└── belongsTo(ProductBatch)

Supplier
├── hasMany(Product)
├── hasMany(PurchaseOrder)
├── pendingOrders() [scoped]
└── totalSpent() [method]

PurchaseOrder
├── belongsTo(Supplier)
├── belongsTo(User)
└── hasMany(PurchaseOrderItem)

Customer
├── hasMany(Transaction)
├── hasMany(CustomerCreditTransaction)
├── availableCredit() [method]
└── isOverdue() [method]

CustomerCreditTransaction
├── belongsTo(Customer)
├── belongsTo(Transaction)
└── belongsTo(User as creator)
```

---

## Controllers Quick Map

| Controller | Methods | Main Actions |
|-----------|---------|--------------|
| **AuthController** | showLogin, login, showSetup, setup, logout | Authentication |
| **DashboardController** | index, stats | Dashboard view & stats API |
| **ProductController** | index, create, store, edit, update, destroy, apiIndex, search, apiStore, apiUpdate, apiDestroy | Product CRUD + API |
| **BatchController** | index, store, expiring, expired, apiStore | Batch management |
| **PosController** | index | POS interface view |
| **TransactionController** | index, show, complete, today, recent, apiShow, processReturn | Transactions + sales API |
| **CustomerController** | index, create, store, show, edit, update, destroy, showPayment, recordPayment, showAdjustment, recordAdjustment | Customers + credit |
| **SupplierController** | index, create, store, show, edit, update, destroy, apiIndex, apiStore | Suppliers CRUD + API |
| **PurchaseOrderController** | index, create, store, show, showReceive, receive, cancel, apiShow | PO workflow + API |
| **UserController** | index, create, store, apiIndex, apiStore | Users CRUD + API |
| **ReportController** | index, sales, profit, inventory, topProducts, suppliers, customers | 6 report types |
| **AnalyticsController** | index, salesData, getSalesTrendData, ... | Analytics dashboard + API |

---

## Service Methods Reference

### PosService
```php
processSale(array $items, array $data)
  // Input: items with product_id, quantity, unit_price
  //        data with payment_method, discount, amount_paid
  //        optional: customer_id, is_credit
  // Output: Transaction with items loaded
  // Side effects: Creates transaction, items, updates stock, updates balance

processReturn(Transaction $originalTransaction)
  // Input: Original SALE transaction
  // Output: RETURN transaction (inverse values)
  // Side effects: Restores inventory, reverses credit
```

### InventoryService
```php
getLowStockProducts()
  // Returns: Products where current_stock <= min_stock

getExpiringBatches($days = 30)
  // Returns: Batches expiring within X days

getExpiredBatches()
  // Returns: Already expired batches

getBestBatchForProduct(Product $product, int $quantity)
  // Returns: Best batch for FIFO consumption (by expiry_date)

deductStock(Product $product, ProductBatch $batch = null, int $quantity)
  // Updates: batch.quantity_remaining--, product.current_stock--

addStock(Product $product, ProductBatch $batch = null, int $quantity)
  // Updates: batch.quantity_remaining++, product.current_stock++

getAllAlerts()
  // Returns: Array with low_stock, expiring_soon, expired, total_alerts
```

### PurchaseOrderService
```php
createPurchaseOrder(array $data)
  // Input: supplier_id, items, order_date, tax, shipping
  // Output: PurchaseOrder with items (status: PENDING)
  // Generates: PO number (PO-000001 format)

receiveStock(PurchaseOrder $po, array $data)
  // Input: items with quantity_received, batch_number, expiry_date
  // Output: Updated PurchaseOrder (status: RECEIVED)
  // Creates: ProductBatches, updates Product.current_stock

markAsOrdered(PurchaseOrder $po)
  // Updates: status PENDING -> ORDERED
```

---

## Common Queries (Scopes)

```php
// Products
Product::search($q)                    // name/sku/description
Product::where('is_active', true)      // Active only

// Batches
ProductBatch::expired()                // expiry_date < today
ProductBatch::expiringSoon($days)      // Within X days

// Transactions
Transaction::sales()                   // type = SALE
Transaction::returns()                 // type = RETURN
Transaction::today()                   // created today

// Purchase Orders
PurchaseOrder::pending()               // status = PENDING
PurchaseOrder::received()              // status = RECEIVED
PurchaseOrder::dateRange($start, $end) // Between dates

// Customers
Customer::search($q)                   // name/phone/email
Customer::active()                     // is_active = true
Customer::creditEnabled()              // credit_enabled = true

// Suppliers
Supplier::active()                     // is_active = true
Supplier::search($q)                   // name/email/phone
```

---

## API Endpoints Summary

### Sales & POS
- `POST /api/transactions` - Complete sale
- `GET /api/transactions/today` - Today's transactions
- `GET /api/transactions/{id}` - Transaction details
- `POST /api/transactions/{id}/return` - Process return

### Products
- `GET /api/products` - List active products
- `GET /api/products/search?q=keyword` - Search products
- `POST /api/products` - Create product
- `POST /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product

### Batches
- `POST /api/products/{product}/batches` - Add batch
- `GET /api/batches/expiring?days=30` - Expiring batches
- `GET /api/batches/expired` - Expired batches

### Dashboard
- `GET /api/dashboard/stats` - Dashboard statistics
- `GET /api/analytics/sales?period=7days|30days|90days` - Sales trends

### Users
- `GET /api/users` - List users (owner, manager only)
- `POST /api/users` - Create user (owner, manager only)

---

## Role-Based Access

```
OWNER (owner)
├─ All CRUD operations
├─ User management
├─ Supplier management
├─ Purchase orders
├─ All reports
└─ All analytics

MANAGER (manager)
├─ Supplier CRUD
├─ Purchase orders
├─ Cannot manage users
├─ All cashier features
└─ Reports & analytics (read-only)

CASHIER (cashier)
├─ POS transactions
├─ View/manage customers
├─ Credit operations
├─ View transactions
├─ Reports (read-only)
└─ Cannot: manage suppliers/users/create POs
```

---

## Common Workflows

### Create & Sell Product
1. `POST /products` - Create product with SKU, prices
2. `POST /purchase-orders` - Create PO with supplier
3. `POST /purchase-orders/{id}/receive` - Receive stock & create batches
4. `POST /api/transactions` - Sell to customer (batch auto-selected via FIFO)

### Customer Credit Sale
1. `POST /customers` - Create customer, enable credit, set limit
2. `POST /api/transactions` - Sale with customer_id, is_credit=true
3. `POST /customers/{id}/payment` - Receive payment
4. `POST /customers/{id}/adjustment` - Manual adjustments if needed

### Daily POS Operations
1. `GET /` - View dashboard
2. `GET /pos` - Open POS interface
3. `POST /api/transactions` - Process sales
4. `POST /api/transactions/{id}/return` - Process returns
5. `GET /api/transactions/today` - View today's sales

### Reporting
1. `GET /reports/sales` - Sales report (date range)
2. `GET /reports/profit` - Profit analysis
3. `GET /reports/inventory` - Stock levels
4. `GET /reports/customers` - Credit status
5. `GET /analytics` - Dashboard with charts

---

## Database Statistics

| Metric | Count |
|--------|-------|
| Core Tables | 10 |
| System Tables | 3 |
| Models | 10 |
| Controllers | 12 |
| Services | 3 |
| Views | 38 |
| Web Routes | 50 |
| API Endpoints | 45 |
| Indexes | 50+ |
| Foreign Keys | 20+ |

---

## File Locations Cheat Sheet

| Component | Location |
|-----------|----------|
| **Models** | `/app/Models/` |
| **Controllers** | `/app/Http/Controllers/` |
| **Services** | `/app/Services/` |
| **Migrations** | `/database/migrations/` |
| **Views** | `/resources/views/` |
| **Web Routes** | `/routes/web.php` |
| **API Routes** | `/routes/api.php` |
| **Config** | `/config/` |

---

## Key Design Principles

1. **FIFO Inventory** - First In, First Out by expiry date
2. **Batch Tracking** - Every item tracked at batch level
3. **Audit Trail** - All credit changes recorded
4. **Atomic Transactions** - DB::transaction() for consistency
5. **Role-Based Access** - Three-tier permission system
6. **Soft Deletes** - Data preservation, not physical deletion
7. **Service Layer** - Business logic separate from HTTP
8. **API-First Design** - JSON endpoints for POS integration

---

## Status Values

### Transactions
- `SALE` - Normal sale
- `RETURN` - Return transaction

### Purchase Orders
- `PENDING` - Newly created, awaiting confirmation
- `ORDERED` - Confirmed and sent to supplier
- `RECEIVED` - Stock received and added to inventory
- `CANCELLED` - Order cancelled (only from PENDING/ORDERED)

### Payment Methods
- `CASH` - Cash payment
- `CARD` - Card payment
- `MOBILE` - Mobile money
- `OTHER` - Other method

### Customer Credit Types
- `SALE` - Credit from sale
- `PAYMENT` - Payment against credit
- `ADJUSTMENT` - Manual adjustment

### User Roles
- `owner` - Full access
- `manager` - Suppliers, POs, users
- `cashier` - POS only

---

**Quick Tip:** Use `/api/products/search?q=paracetamol` to quickly find products from POS or mobile apps!

**Last Updated:** November 19, 2025

# BLORIEN PHARMA SYSTEM - COMPREHENSIVE REVIEW REPORT

**Document Version:** 1.0  
**System:** BLORIEN Pharma Management System (Laravel-based)  
**Review Date:** November 19, 2025

---

## TABLE OF CONTENTS
1. [Executive Summary](#executive-summary)
2. [Database Schema Overview](#database-schema-overview)
3. [Core Features Implemented](#core-features-implemented)
4. [Business Logic and Services](#business-logic-and-services)
5. [User Interfaces and Workflows](#user-interfaces-and-workflows)
6. [API Endpoints](#api-endpoints)
7. [Architectural Patterns and Decisions](#architectural-patterns-and-decisions)
8. [User Roles and Access Control](#user-roles-and-access-control)

---

## EXECUTIVE SUMMARY

BLORIEN Pharma is a comprehensive pharmaceutical management system built with Laravel that handles:
- **Point of Sale (POS)** operations with transaction management
- **Inventory Management** with batch tracking and expiry monitoring
- **Purchase Order Management** with supplier integration
- **Customer Credit System** with balance tracking
- **Advanced Reporting** with sales, profit, and inventory analytics
- **Multi-user Dashboard** with role-based access control

The system is designed for small to medium pharmaceutical retailers and wholesalers, with a focus on batch tracking (critical for pharmaceutical products) and customer credit management.

---

## DATABASE SCHEMA OVERVIEW

### Core Tables and Relationships

#### 1. **Users Table**
```
users (id, name, email, password, role, is_active, email_verified_at, created_at, updated_at)
├── Roles: owner, manager, cashier
├── Indexes: email, role, is_active
└── Purpose: User authentication and authorization
```

#### 2. **Products Table**
```
products (id, name, sku, supplier_id, description, purchase_price, selling_price, 
          current_stock, min_stock, is_active, created_at, updated_at, deleted_at)
├── Relations:
│   ├── supplier_id → suppliers (nullable)
│   ├── hasMany(ProductBatch)
│   └── hasMany(TransactionItem)
├── Indexes: sku, name, is_active, current_stock, supplier_id
├── Soft Deletes: Yes
└── Purpose: Core product catalog with pricing and stock tracking
```

#### 3. **Product Batches Table**
```
product_batches (id, product_id, batch_number, expiry_date, quantity_received, 
                 quantity_remaining, purchase_price, is_active, created_at, updated_at)
├── Relations:
│   ├── belongsTo(Product) - cascade delete
│   └── hasMany(TransactionItem)
├── Indexes: batch_number, expiry_date, [product_id, batch_number], quantity_remaining
├── FIFO Method: Orders by expiry_date (ascending) for stock consumption
└── Purpose: Batch-level inventory tracking critical for pharmaceuticals
```

#### 4. **Transactions Table**
```
transactions (id, type, user_id, customer_id, related_transaction_id, subtotal, 
              tax, discount, total, payment_method, amount_paid, change_given, 
              is_credit, notes, created_at, updated_at)
├── Type: SALE, RETURN
├── Payment Methods: CASH, CARD, MOBILE, OTHER
├── Relations:
│   ├── belongsTo(User) - cascade delete
│   ├── belongsTo(Customer, nullable) - set null
│   ├── hasMany(TransactionItem)
│   ├── belongsTo(Transaction, related_transaction_id) - for returns
│   └── hasMany(Transaction, related_transaction_id) - return tracking
├── Indexes: type, user_id, created_at, payment_method, customer_id, is_credit
└── Purpose: Sales and return transactions with payment tracking
```

#### 5. **Transaction Items Table**
```
transaction_items (id, transaction_id, product_id, batch_id, quantity, unit_price, 
                   subtotal, discount, total, created_at, updated_at)
├── Relations:
│   ├── belongsTo(Transaction) - cascade delete
│   ├── belongsTo(Product) - cascade delete
│   └── belongsTo(ProductBatch, nullable) - set null
├── Indexes: transaction_id, product_id, batch_id
└── Purpose: Line-item detail for transactions
```

#### 6. **Suppliers Table**
```
suppliers (id, name, company_name, email, phone, address, city, country, 
           tax_id, notes, is_active, created_at, updated_at, deleted_at)
├── Relations:
│   ├── hasMany(Product)
│   └── hasMany(PurchaseOrder)
├── Indexes: name, email, phone, is_active
├── Soft Deletes: Yes
└── Purpose: Vendor/supplier information management
```

#### 7. **Purchase Orders Table**
```
purchase_orders (id, po_number (unique), supplier_id, user_id, status, order_date, 
                 expected_delivery_date, received_date, subtotal, tax, shipping, 
                 total, notes, created_at, updated_at, deleted_at)
├── Status: PENDING, ORDERED, RECEIVED, CANCELLED
├── Relations:
│   ├── belongsTo(Supplier) - cascade delete
│   ├── belongsTo(User) - cascade delete
│   └── hasMany(PurchaseOrderItem)
├── Indexes: po_number, supplier_id, status, order_date
├── Soft Deletes: Yes
└── Purpose: Purchase order lifecycle management
```

#### 8. **Purchase Order Items Table**
```
purchase_order_items (id, purchase_order_id, product_id, quantity_ordered, 
                      quantity_received, unit_price, subtotal, batch_number, 
                      expiry_date, created_at, updated_at)
├── Relations:
│   ├── belongsTo(PurchaseOrder) - cascade delete
│   └── belongsTo(Product) - cascade delete
├── Indexes: purchase_order_id, product_id
└── Purpose: Line items for purchase orders
```

#### 9. **Customers Table**
```
customers (id, name, phone, email, address, city, id_number, credit_limit, 
           current_balance, credit_enabled, is_active, notes, created_at, 
           updated_at, deleted_at)
├── Relations:
│   ├── hasMany(Transaction)
│   └── hasMany(CustomerCreditTransaction)
├── Indexes: phone, email, is_active, credit_enabled
├── Soft Deletes: Yes
└── Purpose: Customer information and credit management
```

#### 10. **Customer Credit Transactions Table**
```
customer_credit_transactions (id, customer_id, transaction_id, user_id, type, 
                             amount, balance_before, balance_after, payment_method, 
                             notes, created_at, updated_at)
├── Type: SALE, PAYMENT, ADJUSTMENT
├── Relations:
│   ├── belongsTo(Customer) - cascade delete
│   ├── belongsTo(Transaction, nullable) - set null
│   └── belongsTo(User) - cascade delete
├── Indexes: customer_id, transaction_id, type, created_at
└── Purpose: Audit trail for customer credit transactions
```

### Data Structure Relationships Diagram

```
Users
  ├─→ Transactions (hasMany)
  ├─→ PurchaseOrders (hasMany)
  └─→ CustomerCreditTransactions (hasMany as creator)

Products
  ├─→ ProductBatches (hasMany)
  ├─→ TransactionItems (hasMany)
  ├─→ Supplier (belongsTo)
  └─→ PurchaseOrderItems (hasMany)

ProductBatches
  ├─→ Product (belongsTo, cascade)
  └─→ TransactionItems (hasMany)

Transactions
  ├─→ TransactionItems (hasMany)
  ├─→ User (belongsTo)
  ├─→ Customer (belongsTo, nullable)
  ├─→ Related Transaction (belongsTo/hasMany for returns)
  └─→ CustomerCreditTransactions (hasMany)

Suppliers
  ├─→ Products (hasMany)
  └─→ PurchaseOrders (hasMany)

PurchaseOrders
  ├─→ PurchaseOrderItems (hasMany)
  ├─→ Supplier (belongsTo)
  └─→ User (belongsTo)

Customers
  ├─→ Transactions (hasMany)
  └─→ CustomerCreditTransactions (hasMany)
```

---

## CORE FEATURES IMPLEMENTED

### 1. **Point of Sale (POS) System**

**Capabilities:**
- Real-time transaction processing
- Multi-item cart functionality
- Batch-aware inventory deduction (FIFO method)
- Multiple payment methods (CASH, CARD, MOBILE, OTHER)
- Discount application
- Change calculation
- Transaction history and receipt viewing

**Files:**
- Controller: `/home/user/pharma/app/app/Http/Controllers/PosController.php`
- Service: `/home/user/pharma/app/app/Services/PosService.php`
- View: `/home/user/pharma/app/resources/views/pos/index.blade.php`

### 2. **Inventory Management**

**Capabilities:**
- Batch-level stock tracking
- FIFO (First-In-First-Out) consumption by expiry date
- Low stock alerting
- Expiry date tracking (expired and expiring soon)
- Stock level synchronization between products and batches
- Automatic stock updates on transactions
- Product search and filtering

**Files:**
- Service: `/home/user/pharma/app/app/Services/InventoryService.php`
- Model: `/home/user/pharma/app/app/Models/ProductBatch.php`

**Key Features:**
- `getLowStockProducts()` - Products below minimum threshold
- `getExpiringBatches($days)` - Batches expiring within X days
- `getExpiredBatches()` - Already expired batches
- `getBestBatchForProduct()` - FIFO selection
- `getAllAlerts()` - Combined alert summary

### 3. **Purchase Order Management**

**Capabilities:**
- Create purchase orders with items
- Status tracking (PENDING → ORDERED → RECEIVED → CANCELLED)
- Receive stock with batch details
- Automatic product stock updates
- Supplier tracking
- Expected delivery dates
- Order cost calculation (subtotal + tax + shipping)

**Files:**
- Controller: `/home/user/pharma/app/app/Http/Controllers/PurchaseOrderController.php`
- Service: `/home/user/pharma/app/app/Services/PurchaseOrderService.php`
- Models: `/home/user/pharma/app/app/Models/PurchaseOrder.php`, `PurchaseOrderItem.php`

**Workflow:**
```
Create PO (PENDING)
    ↓
Mark as Ordered (ORDERED)
    ↓
Receive Stock Form
    ↓
Process Receipt (RECEIVED) - Creates ProductBatches, updates stock
```

### 4. **Customer Credit Management**

**Capabilities:**
- Enable/disable credit per customer
- Set credit limits
- Track current balance
- Credit usage validation
- Payment recording
- Manual balance adjustments
- Credit transaction audit trail
- Overdue customer detection

**Files:**
- Controller: `/home/user/pharma/app/app/Http/Controllers/CustomerController.php`
- Models: `/home/user/pharma/app/app/Models/Customer.php`, `CustomerCreditTransaction.php`

**Credit Logic:**
- Available Credit = Credit Limit - Current Balance
- Sale on Credit = increases current_balance
- Payment = decreases current_balance
- Adjustment = manual modification (with notes)

### 5. **Transaction Management**

**Capabilities:**
- Sale transactions with itemization
- Return transactions (linked to original sales)
- Inventory restoration on returns
- Credit reversal on returns
- Payment method tracking
- User attribution
- Transaction detail viewing
- Today's sales tracking

**Features:**
- Negative values for returns maintain audit trail
- Related transaction linking for returns
- Automatic inventory reversal

### 6. **Product Management**

**Capabilities:**
- Create/read/update/delete products
- SKU management (unique constraint)
- Supplier assignment
- Pricing (purchase and selling)
- Stock level initialization
- Minimum stock threshold
- Batch management per product
- Search functionality (name, SKU, description)
- Soft delete support

**Files:**
- Controller: `/home/user/pharma/app/app/Http/Controllers/ProductController.php`
- Model: `/home/user/pharma/app/app/Models/Product.php`

### 7. **Reporting & Analytics**

**Report Types:**

a) **Sales Report**
   - Date range filtering
   - Transaction listing
   - Total sales, discounts, transaction count
   - Average transaction value
   - Sales by date grouping
   - Payment method breakdown

b) **Profit Report**
   - Revenue calculation (transaction totals)
   - Cost calculation (quantity × purchase_price)
   - Profit and margin analysis
   - Per-product profit breakdown
   - Top profitable products

c) **Inventory Report**
   - Current stock levels
   - Inventory value (cost-based)
   - Retail value (selling price-based)
   - Potential profit calculation
   - Low stock identification
   - Products ranked by inventory value

d) **Top Products Report**
   - Period filtering (week/month/year)
   - Quantity sold ranking
   - Revenue contribution
   - Average selling price

e) **Supplier Performance Report**
   - Order count by supplier
   - Total spending per supplier
   - Received vs pending orders
   - Date range filtering

f) **Customer Credit Report**
   - Credit-enabled customers only
   - Credit limit and balance
   - Available credit calculation
   - Utilization percentage
   - Overdue customer flagging
   - Transaction count

g) **Analytics Dashboard**
   - 30-day sales trend
   - Payment method breakdown (monthly)
   - Top 10 products by revenue
   - Inventory status pie chart
   - Customer credit utilization
   - Month-over-month comparison
   - Real-time sales data API

**Files:**
- Controllers: `/home/user/pharma/app/app/Http/Controllers/ReportController.php`, `AnalyticsController.php`

### 8. **User Management**

**Capabilities:**
- User creation with roles
- Role-based access control
- User activation/deactivation
- Password hashing (Laravel default)
- Email-based login

**User Roles:**
1. **Owner** - Full system access
2. **Manager** - Suppliers, purchase orders, user management
3. **Cashier** - POS, transactions, products, customers only

---

## BUSINESS LOGIC AND SERVICES

### PosService

**Location:** `/home/user/pharma/app/app/Services/PosService.php`

**Key Methods:**

1. **`processSale(array $items, array $data)`**
   - Validates stock availability
   - Uses FIFO batch selection
   - Calculates totals (subtotal - discount)
   - Handles credit sales:
     - Validates credit availability
     - Updates customer balance
     - Creates audit trail
   - Creates Transaction + TransactionItems
   - Updates inventory atomically (DB transaction)
   - Returns: Transaction with loaded relationships

2. **`processReturn(Transaction $originalTransaction)`**
   - Validates it's a SALE transaction
   - Creates inverse-value RETURN transaction
   - Links original transaction
   - Reverses inventory for all items
   - Credit reversal for credit sales
   - Maintains audit trail with negative values

**Database Transaction Safety:**
- All operations wrapped in `DB::transaction()`
- Atomic consistency for complex multi-table updates

### InventoryService

**Location:** `/home/user/pharma/app/app/Services/InventoryService.php`

**Key Methods:**

1. **`getLowStockProducts()`**
   - Returns products where current_stock ≤ min_stock
   - Active products only
   - Ordered by stock level

2. **`getExpiringBatches($days = null)`**
   - Default: 30 days from today
   - Expiring batches with remaining quantity > 0
   - Ordered by expiry_date

3. **`getExpiredBatches()`**
   - Batches where expiry_date < today()
   - With remaining quantity > 0

4. **`deductStock(Product $product, ProductBatch $batch = null, int $quantity)`**
   - Validates quantity > 0
   - Decrements batch.quantity_remaining if batch specified
   - Always decrements product.current_stock
   - Exception handling for insufficient stock

5. **`addStock(Product $product, ProductBatch $batch = null, int $quantity)`**
   - Inverse of deductStock
   - Used for returns and adjustments

6. **`getBestBatchForProduct(Product $product, int $quantity)`**
   - FIFO selection: orders by expiry_date ascending
   - Requires batch to have sufficient quantity_remaining
   - Returns first matching batch or null

7. **`getAllAlerts()`**
   - Returns combined alert summary:
     - low_stock: count + items
     - expiring_soon: count + items
     - expired: count + items
     - total_alerts: sum

8. **`getProductStockSummary(Product $product)`**
   - Detailed stock analysis
   - Detects stock mismatches (product.current_stock != sum of batches)

### PurchaseOrderService

**Location:** `/home/user/pharma/app/app/Services/PurchaseOrderService.php`

**Key Methods:**

1. **`createPurchaseOrder(array $data)`**
   - Calculates subtotal from items
   - Applies tax and shipping
   - Generates unique PO number (PO-000001 format)
   - Creates PurchaseOrder with PENDING status
   - Creates PurchaseOrderItems
   - Returns: Loaded PurchaseOrder with relationships

2. **`receiveStock(PurchaseOrder $purchaseOrder, array $data)`**
   - Updates PurchaseOrder status → RECEIVED
   - For each item:
     - Updates quantity_received
     - Records batch_number and expiry_date
     - Creates ProductBatch entry
     - Increments Product.current_stock
     - Updates product purchase_price if different
   - Atomic DB transaction
   - Returns: Updated PurchaseOrder

3. **`markAsOrdered(PurchaseOrder $purchaseOrder)`**
   - Status validation: only PENDING → ORDERED
   - Throws exception for invalid states

---

## USER INTERFACES AND WORKFLOWS

### 1. **Authentication Workflow**

**Initial Setup (No Users Exist)**
```
GET /setup
    ↓
Display Setup Form
    ↓
POST /setup (name, email, password, password_confirmation)
    ↓
Create Owner Account
    ↓
Auto-login & Redirect to Dashboard
```

**Regular Login**
```
GET /login
    ↓
Display Login Form
    ↓
POST /login (email, password, remember)
    ↓
Validate Credentials
    ↓
Check is_active flag
    ↓
Redirect to Dashboard or Login with error
```

### 2. **Dashboard Workflow**

**View: `/home/user/pharma/app/resources/views/dashboard/index.blade.php`**

Displays:
- Total active products
- Low stock product count
- Expired batch count
- Expiring soon batch count
- Today's total sales
- Today's transaction count
- Last 5 transactions (recent activity)

### 3. **POS Transaction Workflow**

**View: `/home/user/pharma/app/resources/views/pos/index.blade.php`**

```
Start Transaction
    ↓
Search/Select Products
    ↓
Add to Cart (with batch selection via FIFO)
    ↓
Set Payment Method
    ↓
Apply Discount (optional)
    ↓
Select Payment Type:
    ├─ Cash Sale:
    │   ├─ Enter Amount Paid
    │   └─ Calculate Change
    └─ Credit Sale:
        ├─ Select Customer
        ├─ Validate Credit Available
        └─ Record as Credit
    ↓
Complete Transaction (API Call)
    ↓
Success/Receipt View or Error
```

**API Endpoint:** `POST /api/transactions`

### 4. **Purchase Order Workflow**

**View: `/home/user/pharma/app/resources/views/purchase-orders/`**

```
Create PO (role: owner, manager)
    ↓
Select Supplier
    ↓
Add Items:
    ├─ Product
    ├─ Quantity
    └─ Unit Price
    ↓
Set Order Date & Expected Delivery
    ↓
Add Tax & Shipping (optional)
    ↓
Create (PO Number auto-generated)
    ↓
PO List View
    ↓
Click PO → View Details
    ↓
Mark as Ordered (optional status)
    ↓
Receive Stock:
    ├─ For each item:
    │   ├─ Quantity Received
    │   ├─ Batch Number
    │   └─ Expiry Date
    │
    ├─ Creates ProductBatch
    ├─ Updates Product.current_stock
    └─ Sets PO status to RECEIVED
    ↓
Inventory Updated
```

### 5. **Customer Credit Workflow**

**View: `/home/user/pharma/app/resources/views/customers/`**

```
Create Customer
    ↓
Enable Credit (checkbox)
    ↓
Set Credit Limit
    ↓
Customer Shows in Credit-enabled List
    ↓
POS Transaction:
    ├─ For Credit Sale:
    │   ├─ Validate credit_enabled = true
    │   ├─ Check availableCredit >= transaction total
    │   ├─ Record sale with is_credit = true
    │   ├─ Create CustomerCreditTransaction (type: SALE)
    │   └─ Increment current_balance
    │
    └─ For Cash Sale: No credit impact
    ↓
Customer Payment:
    ├─ Show Payment Form
    ├─ Enter Amount ≤ current_balance
    ├─ Select Payment Method
    ├─ Create CustomerCreditTransaction (type: PAYMENT)
    └─ Decrement current_balance
    ↓
Manual Adjustment (Owner/Manager):
    ├─ Enter Adjustment Amount (positive/negative)
    ├─ Required Reason/Notes
    ├─ Create CustomerCreditTransaction (type: ADJUSTMENT)
    └─ Update current_balance
    ↓
View Credit History:
    ├─ Paginated list of credit transactions
    ├─ Type, Amount, Balance Before/After
    ├─ Created by user attribution
    └─ Notes
```

### 6. **Product Management Workflow**

**Views:**
- Index: `/home/user/pharma/app/resources/views/products/index.blade.php`
- Create: `/home/user/pharma/app/resources/views/products/create.blade.php`
- Edit: `/home/user/pharma/app/resources/views/products/edit.blade.php`
- Batches: `/home/user/pharma/app/resources/views/products/batches.blade.php`

```
Product CRUD Operations
    ├─ List with Search (name/SKU/description)
    ├─ Create New
    │   ├─ Name, SKU (unique)
    │   ├─ Description
    │   ├─ Purchase & Selling Prices
    │   ├─ Current Stock
    │   ├─ Min Stock Threshold
    │   └─ Active Status
    ├─ Edit Existing
    ├─ Soft Delete
    └─ Batch Management per Product
        ├─ View Batches
        ├─ Add Batch:
        │   ├─ Batch Number
        │   ├─ Expiry Date
        │   ├─ Quantity
        │   └─ Purchase Price
        └─ Batch sorted by expiry_date
```

---

## API ENDPOINTS

### Authentication

| Method | Endpoint | Purpose | Auth |
|--------|----------|---------|------|
| GET | `/login` | Show login form | No |
| POST | `/login` | Handle login | No |
| GET | `/setup` | Show setup form | No |
| POST | `/setup` | Initialize system | No |
| POST | `/logout` | Logout user | Yes |

### Dashboard & Analytics

| Method | Endpoint | Purpose | Auth |
|--------|----------|---------|------|
| GET | `/` | Dashboard HTML | Yes |
| GET | `/dashboard` | Dashboard HTML | Yes |
| GET | `/api/dashboard/stats` | Dashboard statistics JSON | Yes |
| GET | `/analytics` | Analytics dashboard HTML | Yes |
| GET | `/api/analytics/sales` | Sales data JSON (period: 7days/30days/90days) | Yes |

### Products

| Method | Endpoint | Purpose | Auth |
|--------|----------|---------|------|
| GET | `/products` | List products HTML | Yes |
| POST | `/products` | Create product | Yes |
| GET | `/products/create` | Create form HTML | Yes |
| PUT | `/products/{id}` | Update product | Yes |
| GET | `/products/{id}/edit` | Edit form HTML | Yes |
| DELETE | `/products/{id}` | Delete product | Yes |
| **API Endpoints** | | | |
| GET | `/api/products` | List active products JSON | Yes |
| GET | `/api/products/search` | Search products (q param) | Yes |
| POST | `/api/products` | Create product API | Yes |
| POST | `/api/products/{id}` | Update product API | Yes |
| DELETE | `/api/products/{id}` | Delete product API | Yes |

### Batches

| Method | Endpoint | Purpose | Auth |
|--------|----------|---------|------|
| GET | `/products/{product}/batches` | List batches HTML | Yes |
| POST | `/products/{product}/batches` | Create/add batch | Yes |
| **API Endpoints** | | | |
| POST | `/api/products/{product}/batches` | Create batch API | Yes |
| GET | `/api/batches/expiring` | Get expiring batches (days param) | Yes |
| GET | `/api/batches/expired` | Get expired batches | Yes |

### Transactions & POS

| Method | Endpoint | Purpose | Auth |
|--------|----------|---------|------|
| GET | `/pos` | POS interface HTML | Yes |
| GET | `/transactions` | List transactions HTML | Yes |
| GET | `/transactions/{id}` | View transaction detail HTML | Yes |
| **API Endpoints** | | | |
| POST | `/api/transactions` | Complete sale (items, payment_method, discount, amount_paid) | Yes |
| GET | `/api/transactions/today` | Today's transactions JSON | Yes |
| GET | `/api/transactions/recent` | Recent transactions JSON (last 10) | Yes |
| GET | `/api/transactions/{id}` | Transaction detail JSON | Yes |
| POST | `/api/transactions/{id}/return` | Process return | Yes |

### Suppliers

| Method | Endpoint | Purpose | Auth | Role |
|--------|----------|---------|------|------|
| GET | `/suppliers` | List suppliers HTML | Yes | owner, manager |
| POST | `/suppliers` | Create supplier | Yes | owner, manager |
| GET | `/suppliers/create` | Create form HTML | Yes | owner, manager |
| GET | `/suppliers/{id}` | View supplier HTML | Yes | owner, manager |
| PUT | `/suppliers/{id}` | Update supplier | Yes | owner, manager |
| GET | `/suppliers/{id}/edit` | Edit form HTML | Yes | owner, manager |
| DELETE | `/suppliers/{id}` | Delete supplier | Yes | owner, manager |
| **API Endpoints** | | | | |
| GET | `/api/suppliers` | List active suppliers JSON | Yes | Any |
| POST | `/api/suppliers` | Create supplier API | Yes | owner, manager |

### Purchase Orders

| Method | Endpoint | Purpose | Auth | Role |
|--------|----------|---------|------|------|
| GET | `/purchase-orders` | List POs HTML | Yes | owner, manager |
| POST | `/purchase-orders` | Create PO | Yes | owner, manager |
| GET | `/purchase-orders/create` | Create form HTML | Yes | owner, manager |
| GET | `/purchase-orders/{id}` | View PO HTML | Yes | owner, manager |
| GET | `/purchase-orders/{id}/receive` | Receive form HTML | Yes | owner, manager |
| POST | `/purchase-orders/{id}/receive` | Process receipt | Yes | owner, manager |
| PUT | `/purchase-orders/{id}/cancel` | Cancel PO | Yes | owner, manager |
| **API Endpoints** | | | | |
| GET | `/api/purchase-orders/{id}` | PO detail JSON | Yes | owner, manager |

### Customers

| Method | Endpoint | Purpose | Auth |
|--------|----------|---------|------|
| GET | `/customers` | List customers HTML | Yes |
| POST | `/customers` | Create customer | Yes |
| GET | `/customers/create` | Create form HTML | Yes |
| GET | `/customers/{id}` | View customer HTML | Yes |
| PUT | `/customers/{id}` | Update customer | Yes |
| GET | `/customers/{id}/edit` | Edit form HTML | Yes |
| DELETE | `/customers/{id}` | Delete customer | Yes |
| GET | `/customers/{id}/payment` | Payment form HTML | Yes |
| POST | `/customers/{id}/payment` | Record payment | Yes |
| GET | `/customers/{id}/adjustment` | Adjustment form HTML | Yes |
| POST | `/customers/{id}/adjustment` | Record adjustment | Yes |

### Reports

| Method | Endpoint | Purpose | Auth |
|--------|----------|---------|------|
| GET | `/reports` | Reports dashboard HTML | Yes |
| GET | `/reports/sales` | Sales report (start_date, end_date) | Yes |
| GET | `/reports/profit` | Profit report (start_date, end_date) | Yes |
| GET | `/reports/inventory` | Inventory report | Yes |
| GET | `/reports/top-products` | Top products (period: week/month/year) | Yes |
| GET | `/reports/suppliers` | Supplier report (start_date, end_date) | Yes |
| GET | `/reports/customers` | Customer credit report | Yes |

### Users

| Method | Endpoint | Purpose | Auth | Role |
|--------|----------|---------|------|------|
| GET | `/users` | List users HTML | Yes | owner, manager |
| POST | `/users` | Create user | Yes | owner, manager |
| GET | `/users/create` | Create form HTML | Yes | owner, manager |
| **API Endpoints** | | | | |
| GET | `/api/users` | List users JSON | Yes | owner, manager |
| POST | `/api/users` | Create user API | Yes | owner, manager |

---

## ARCHITECTURAL PATTERNS AND DECISIONS

### 1. **Service Layer Pattern**

**Implementation:**
- Business logic extracted to dedicated Service classes
- Controllers remain thin, delegating to services
- Services handle database transactions and complex operations

**Services:**
- `PosService` - Transaction processing and returns
- `InventoryService` - Stock management and alerts
- `PurchaseOrderService` - PO lifecycle

**Benefits:**
- Reusable business logic
- Easier testing
- Clear separation of concerns
- Decouples HTTP layer from business rules

### 2. **Repository Pattern (Implicit)**

**Implementation:**
- Models encapsulate database queries
- Scopes for common query patterns

**Scopes Used:**
- `Product::search($q)` - Search by name/SKU/description
- `ProductBatch::expired()` - Expired batches
- `ProductBatch::expiringSoon($days)` - Expiring soon
- `Transaction::sales()` - Sales only
- `Transaction::returns()` - Returns only
- `Transaction::today()` - Today's transactions
- `PurchaseOrder::pending()` - Pending orders
- `PurchaseOrder::received()` - Received orders
- `Customer::search($q)` - Search customers
- `Customer::active()` - Active customers
- `Customer::creditEnabled()` - Credit-enabled customers
- `Supplier::active()` - Active suppliers
- `Supplier::search($q)` - Search suppliers

**Benefits:**
- Reusable query logic
- Readable and chainable queries
- DRY principle for database interactions

### 3. **Role-Based Access Control (RBAC)**

**Roles:**
1. **Owner** (owner)
   - Full system access
   - All CRUD operations
   - User management
   - Supplier and PO management

2. **Manager** (manager)
   - Supplier and PO management
   - User management
   - Reports and analytics
   - All cashier capabilities

3. **Cashier** (cashier)
   - POS operations
   - View products, batches
   - View/manage customers and transactions
   - View reports and analytics (read-only)
   - Cannot: manage suppliers, create purchase orders, manage users

**Implementation:**
- `auth()->user()->hasRole($roles)` method in User model
- Middleware: `role:owner,manager` in routes
- is_active flag for additional access control

### 4. **Database Transaction Safety**

**Pattern:**
- All multi-step operations wrapped in `DB::transaction()`
- Ensures ACID compliance for complex state changes
- Automatic rollback on exception

**Used In:**
- `PosService::processSale()` - Transaction creation + inventory update
- `PosService::processReturn()` - Return creation + inventory restoration
- `PurchaseOrderService::receiveStock()` - PO update + batch creation + stock update
- `CustomerController::recordPayment()` - Payment creation + balance update
- `CustomerController::recordAdjustment()` - Adjustment creation + balance update

### 5. **Soft Delete Pattern**

**Implemented On:**
- Products
- Suppliers
- Purchase Orders
- Customers

**Benefits:**
- Data preservation for audit trails
- Relationships can be restored
- Historical data intact
- Filtered out by default (using `withTrashed()` when needed)

**Not Implemented On:**
- Transactions (audit trail purposes)
- TransactionItems (related to transactions)
- ProductBatches (related to transactions)
- Users (critical for access control)

### 6. **FIFO (First In, First Out) Inventory**

**Pattern:**
- Products tracked at batch level
- Batch expiry_date determines consumption order
- `ProductBatch::activeBatches()` orders by expiry_date ascending
- `InventoryService::getBestBatchForProduct()` selects earliest expiry

**Why For Pharma:**
- Pharmaceutical products have shelf lives
- Regulatory compliance (use before expiry)
- Prevents waste from expired stock
- Critical for customer safety

### 7. **Audit Trail Pattern**

**Implementations:**

1. **Customer Credit Transactions**
   - Every credit change recorded in `CustomerCreditTransaction`
   - Type: SALE, PAYMENT, ADJUSTMENT
   - Tracks: amount, balance_before, balance_after, created_by user, notes
   - Immutable (no updates, only inserts)

2. **Negative Values for Returns**
   - Returns use negative values in `Transaction` and `TransactionItem`
   - Maintains complete history
   - Easy to identify returns vs sales
   - Audit trail complete

3. **User Attribution**
   - Transactions linked to creating user
   - PurchaseOrders linked to creating user
   - Credit transactions linked to acting user

### 8. **Calculated Fields vs. Stored Values**

**Strategy:**
- Store: Quantities, prices, individual totals
- Calculate: Aggregates, derived values, statistics

**Stored:**
- `Product::current_stock` - Updated on every transaction
- `ProductBatch::quantity_remaining` - Updated on every deduction
- `Customer::current_balance` - Updated on every credit transaction
- Transaction totals: subtotal, discount, tax, total

**Calculated (On-Demand):**
- `Customer::availableCredit()` = credit_limit - current_balance
- Profit = Revenue - Cost
- Inventory value = quantity × price
- Sales trends, aggregations

**Benefits:**
- Fast queries (no need to recalculate totals)
- Accurate at point of transaction
- Supporting calculations available when needed

### 9. **Payment Method Abstraction**

**Supported Methods:**
- CASH
- CARD
- MOBILE
- OTHER

**Flexibility:**
- No external payment processing integration
- Flexible for future integration
- Records payment method for reporting

### 10. **API Versioning Strategy**

**Current:**
- No explicit versioning
- Single `/api` route group
- All endpoints share same middleware and auth

**Could Be Enhanced:**
- Implement `/api/v1`, `/api/v2` for backwards compatibility
- Currently acceptable for single-version system

### 11. **Error Handling**

**Patterns:**

1. **Validation**
   - Laravel's built-in validation
   - Returns to form with errors for web
   - Returns 422 JSON for API

2. **Business Logic Exceptions**
   - Thrown as `\Exception` with descriptive messages
   - Caught in controllers
   - Returned as 400 JSON error with message

3. **No Try-Catch in Services**
   - Exceptions bubble up to controllers
   - Controllers handle presentation
   - Allows services to remain pure

### 12. **Pagination Strategy**

**Default:**
- Products, Suppliers: 20 items per page
- Transactions: 20 items per page
- Users: 20 items per page
- Customers: 15 items per page
- PurchaseOrders: 20 items per page
- Credit History: 15 items per page

**API:**
- Returns all matching records (no pagination)
- Applies limit for search (e.g., 20 items max)

### 13. **Search Implementation**

**Pattern:**
- Case-insensitive LIKE searches
- Multiple field searching
- Used in models as scope

**Examples:**
```php
Product::search($q) → name, sku, description
Supplier::search($q) → name, company_name, email, phone
Customer::search($q) → name, phone, email
```

---

## USER ROLES AND ACCESS CONTROL

### Role Hierarchy

```
OWNER
├─ Can do: Everything
├─ Suppliers (full CRUD)
├─ Purchase Orders (full workflow)
├─ User Management
├─ All Manager & Cashier capabilities
└─ System Setup

MANAGER
├─ Can do: Most operations (except users)
├─ Suppliers (full CRUD)
├─ Purchase Orders (full workflow)
├─ Cannot: Manage Users
├─ All Cashier capabilities
└─ Reports & Analytics (read-only)

CASHIER
├─ Can do: POS operations
├─ Point of Sale (transactions)
├─ View Products, Batches
├─ View/Manage Customers
├─ View Transactions
├─ View Reports (read-only)
└─ Cannot: Manage Suppliers, Users, Create POs
```

### Feature Access by Role

| Feature | Owner | Manager | Cashier |
|---------|-------|---------|---------|
| POS | ✓ | ✓ | ✓ |
| Products (CRUD) | ✓ | ✓ | ✓ (R) |
| Batches (CRUD) | ✓ | ✓ | ✓ (R) |
| Transactions | ✓ | ✓ | ✓ |
| Returns | ✓ | ✓ | ✓ |
| Customers (CRUD) | ✓ | ✓ | ✓ |
| Credit Payments | ✓ | ✓ | ✓ |
| Credit Adjustments | ✓ | ✓ | - |
| Suppliers (CRUD) | ✓ | ✓ | - |
| Purchase Orders | ✓ | ✓ | - |
| Reports | ✓ | ✓ | ✓ (R) |
| Analytics | ✓ | ✓ | ✓ (R) |
| Users | ✓ | - | - |

**Legend:** ✓ = Full Access, ✓ (R) = Read-only, - = No Access

### Session & Authentication

- **Method:** Email + Password
- **Storage:** Sessions table (Laravel default)
- **Tokens:** Remember tokens for "Remember Me"
- **Validation:** Active status checked on login
- **Deactivation:** Automatic logout if account deactivated
- **Password:** Hashed using Laravel's Hash::make() (bcrypt)

---

## KEY FILES SUMMARY

### Controllers (12 files)
- `AuthController.php` - Login, setup, logout
- `DashboardController.php` - Dashboard stats
- `ProductController.php` - Products CRUD + API
- `BatchController.php` - Batches management
- `PosController.php` - POS interface
- `TransactionController.php` - Transactions + API
- `SupplierController.php` - Suppliers CRUD + API
- `PurchaseOrderController.php` - POs lifecycle + API
- `CustomerController.php` - Customers + credit management
- `UserController.php` - Users CRUD + API
- `ReportController.php` - All reports (6 types)
- `AnalyticsController.php` - Analytics dashboard + API

### Services (3 files)
- `PosService.php` - Sales and returns
- `InventoryService.php` - Stock management
- `PurchaseOrderService.php` - PO workflow

### Models (10 files)
- `User.php` - Users with role checking
- `Product.php` - Products with relationships
- `ProductBatch.php` - Batch tracking with expiry
- `Transaction.php` - Sales/returns
- `TransactionItem.php` - Transaction line items
- `Supplier.php` - Suppliers with statistics
- `PurchaseOrder.php` - PO management
- `PurchaseOrderItem.php` - PO line items
- `Customer.php` - Customers with credit
- `CustomerCreditTransaction.php` - Credit audit trail

### Migrations (8 files)
- `2024_01_01_000001_create_users_table.php`
- `2024_01_01_000002_create_products_table.php`
- `2024_01_01_000003_create_product_batches_table.php`
- `2024_01_01_000004_create_transactions_table.php`
- `2024_01_01_000005_create_transaction_items_table.php`
- `2024_01_01_000006_create_suppliers_table.php`
- `2024_01_01_000007_create_purchase_orders_table.php`
- `2024_01_01_000008_create_customers_table.php`

### Views (38 files - Blade templates)
- **Auth:** login, setup
- **Dashboard:** index, alerts
- **Products:** index, create, edit, batches
- **Transactions:** index, show
- **POS:** index
- **Suppliers:** index, create, edit, show
- **Purchase Orders:** index, create, show, receive
- **Customers:** index, create, edit, show, payment, adjustment
- **Reports:** index, sales, profit, inventory, top-products, suppliers, customers
- **Analytics:** index
- **Users:** index, create
- **Layouts:** app (master template)

### Routes (2 files)
- `web.php` - Web routes (HTML views)
- `api.php` - API routes (JSON responses)

---

## SYSTEM STATISTICS

- **Total Database Tables:** 10 core + 3 system tables (password_reset_tokens, sessions)
- **Total Models:** 10
- **Total Controllers:** 12
- **Total Services:** 3
- **Total Views:** 38
- **Total Migrations:** 8
- **API Endpoints:** ~45 endpoints
- **Web Routes:** ~50 routes
- **Soft Delete Tables:** 4 (Products, Suppliers, PurchaseOrders, Customers)
- **Indexed Columns:** 50+
- **User Roles:** 3
- **Report Types:** 6
- **Payment Methods:** 4
- **Transaction Types:** 2 (SALE, RETURN)

---

## DESIGN STRENGTHS

1. **Batch-Aware Inventory** - Critical for pharmaceutical products
2. **Comprehensive Audit Trail** - Customer credit transactions tracked
3. **FIFO Logic** - Expiry management prevents waste
4. **Role-Based Access** - Three-tier permission system
5. **Transaction Safety** - Database transactions for consistency
6. **Flexible Reporting** - Multiple report types with date filtering
7. **Supplier Integration** - Purchase order workflow with batch tracking
8. **Credit Management** - Complete customer credit system with limits
9. **RESTful API** - JSON endpoints for POS and data access
10. **Soft Deletes** - Data preservation with logical deletion

---

## POTENTIAL ENHANCEMENTS

1. **API Versioning** - Implement `/api/v1`, `/api/v2`
2. **Payment Gateway Integration** - Real transaction processing
3. **Barcode Scanning** - Speed up POS entry
4. **Mobile App** - Dedicated mobile POS
5. **Email Notifications** - Low stock, expiry alerts
6. **SMS Integration** - Customer notifications
7. **Multi-location Support** - Multiple branches/warehouses
8. **Advanced Analytics** - Predictive inventory, sales forecasting
9. **Audit Logging** - System action audit trail
10. **Two-Factor Authentication** - Enhanced security
11. **Batch Import** - CSV product/customer imports
12. **Stock Adjustments** - Manual inventory reconciliation
13. **Expense Tracking** - Operational costs
14. **Inventory Variance** - Physical count reconciliation
15. **Webhook Support** - External system integration

---

**End of Report**

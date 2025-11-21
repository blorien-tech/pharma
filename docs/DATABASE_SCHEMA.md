# BLORIEN Pharma - Database Schema

**Version:** 2.6.0
**Database:** MySQL 8.0
**Charset:** utf8mb4
**Collation:** utf8mb4_unicode_ci
**Last Updated:** November 2025

---

## Table of Contents

1. [Overview](#overview)
2. [Entity Relationship Diagram](#entity-relationship-diagram)
3. [Tables Reference](#tables-reference)
4. [Relationships](#relationships)
5. [Indexes](#indexes)
6. [Migrations](#migrations)

---

## Overview

### Database Statistics

- **Total Tables:** 12
- **Total Relationships:** 25+
- **Total Indexes:** 60+
- **Storage Engine:** InnoDB
- **Foreign Key Constraints:** Enabled

### Design Principles

1. **Normalization** - 3NF compliance
2. **Audit Trails** - Track all critical changes
3. **Soft Deletes** - Preserve data integrity
4. **FIFO Support** - Batch-level inventory
5. **Scalability** - Indexed for performance

---

## Entity Relationship Diagram

```
┌─────────────┐
│    users    │
└──────┬──────┘
       │
       ├──────────────────────────────────┐
       │                                  │
       ▼                                  ▼
┌─────────────────┐            ┌──────────────────┐
│  transactions   │            │ purchase_orders  │
└────────┬────────┘            └────────┬─────────┘
         │                              │
         ├──────────────┐               │
         │              │               │
         ▼              ▼               ▼
┌─────────────────┐  ┌──────┐   ┌───────────────────────┐
│transaction_items│  │ dues │   │purchase_order_items   │
└────────┬────────┘  └──┬───┘   └──────────┬────────────┘
         │              │                   │
         ▼              ▼                   ▼
    ┌─────────┐   ┌────────────┐     ┌──────────┐
    │products │   │due_payments│     │ products │
    └────┬────┘   └────────────┘     └──────────┘
         │
         ├─────────────────┐
         │                 │
         ▼                 ▼
┌────────────────┐  ┌───────────┐
│product_batches │  │ suppliers │
└────────────────┘  └───────────┘

┌───────────┐
│ customers │
└─────┬─────┘
      │
      ├────────────────┬──────────────┐
      │                │              │
      ▼                ▼              ▼
┌─────────────┐  ┌────────────┐  ┌──────┐
│transactions │  │   dues     │  │credit│
│             │  │            │  │trans │
└─────────────┘  └────────────┘  └──────┘
```

---

## Tables Reference

### 1. users

User authentication and authorization.

| Column | Type | Attributes | Description |
|--------|------|-----------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique user ID |
| name | VARCHAR(255) | NOT NULL | Full name |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Email address (login) |
| password | VARCHAR(255) | NOT NULL | Hashed password (bcrypt) |
| role | ENUM | NOT NULL | owner/manager/cashier |
| is_active | BOOLEAN | DEFAULT 1 | Active status |
| language | VARCHAR(5) | DEFAULT 'en' | Preferred language (en/bn) |
| remember_token | VARCHAR(100) | NULL | Session token |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |
| deleted_at | TIMESTAMP | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE (email)
- INDEX (is_active)
- INDEX (role)

**Relationships:**
- hasMany: transactions, purchase_orders, dues, due_payments

---

### 2. products

Medicine catalog with pricing and stock information.

| Column | Type | Attributes | Description |
|--------|------|-----------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Product ID |
| name | VARCHAR(255) | NOT NULL | Brand name |
| generic_name | VARCHAR(255) | NULL | Generic/chemical name |
| brand_name | VARCHAR(255) | NULL | Brand name (alt) |
| sku | VARCHAR(255) | UNIQUE, NOT NULL | Stock Keeping Unit |
| barcode | VARCHAR(255) | NULL | Barcode number |
| supplier_id | BIGINT UNSIGNED | NULL, FK | Default supplier |
| description | TEXT | NULL | Product description |
| purchase_price | DECIMAL(10,2) | NOT NULL | Cost price |
| selling_price | DECIMAL(10,2) | NOT NULL | Retail price |
| current_stock | INT | DEFAULT 0 | Total stock quantity |
| min_stock | INT | DEFAULT 10 | Low stock threshold |
| is_active | BOOLEAN | DEFAULT 1 | Active status |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |
| deleted_at | TIMESTAMP | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE (sku)
- INDEX (barcode)
- INDEX (generic_name)
- INDEX (brand_name)
- INDEX (supplier_id)
- INDEX (is_active)
- INDEX (current_stock)
- INDEX (deleted_at)

**Relationships:**
- belongsTo: supplier
- hasMany: product_batches, transaction_items, purchase_order_items

**Business Logic:**
```sql
-- Low stock check
current_stock <= min_stock

-- Calculate profit margin
((selling_price - purchase_price) / purchase_price) * 100
```

---

### 3. product_batches

FIFO inventory tracking with expiry dates.

| Column | Type | Attributes | Description |
|--------|------|-----------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Batch ID |
| product_id | BIGINT UNSIGNED | NOT NULL, FK | Product reference |
| batch_number | VARCHAR(255) | NOT NULL | Batch identifier |
| expiry_date | DATE | NOT NULL | Expiration date |
| quantity_received | INT | NOT NULL | Initial quantity |
| quantity_remaining | INT | NOT NULL | Current quantity |
| purchase_price | DECIMAL(10,2) | NULL | Batch cost price |
| is_active | BOOLEAN | DEFAULT 1 | Active status |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |

**Indexes:**
- PRIMARY KEY (id)
- INDEX (product_id)
- INDEX (expiry_date)
- INDEX (is_active)
- INDEX (quantity_remaining)

**Relationships:**
- belongsTo: product
- hasMany: transaction_items

**Business Logic:**
```sql
-- FIFO selection
ORDER BY expiry_date ASC

-- Expiring soon (30 days)
WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)

-- Expired
WHERE expiry_date < CURDATE()

-- Depleted
WHERE quantity_remaining = 0
```

---

### 4. transactions

Sales and return transactions.

| Column | Type | Attributes | Description |
|--------|------|-----------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Transaction ID |
| receipt_number | VARCHAR(255) | UNIQUE, NOT NULL | Invoice number |
| user_id | BIGINT UNSIGNED | NOT NULL, FK | User who processed |
| customer_id | BIGINT UNSIGNED | NULL, FK | Customer (optional) |
| type | ENUM | NOT NULL | SALE/RETURN |
| subtotal | DECIMAL(10,2) | NOT NULL | Before discount |
| discount | DECIMAL(10,2) | DEFAULT 0 | Discount amount |
| total | DECIMAL(10,2) | NOT NULL | Final total |
| payment_method | ENUM | NOT NULL | CASH/CARD/MOBILE/CREDIT/OTHER |
| amount_paid | DECIMAL(10,2) | DEFAULT 0 | Amount received |
| change_amount | DECIMAL(10,2) | DEFAULT 0 | Change given |
| is_credit | BOOLEAN | DEFAULT 0 | Credit sale flag |
| return_for_transaction_id | BIGINT UNSIGNED | NULL, FK | Original transaction (if return) |
| created_at | TIMESTAMP | NULL | Sale timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE (receipt_number)
- INDEX (user_id)
- INDEX (customer_id)
- INDEX (type)
- INDEX (payment_method)
- INDEX (created_at)
- INDEX (return_for_transaction_id)

**Relationships:**
- belongsTo: user, customer, return_for (Transaction)
- hasMany: transaction_items, returns (Transaction)

**Business Logic:**
```sql
-- Today's sales
WHERE type = 'SALE' AND DATE(created_at) = CURDATE()

-- Receipt number format
INV-YYYYMMDD-0001
```

---

### 5. transaction_items

Line items for sales and returns.

| Column | Type | Attributes | Description |
|--------|------|-----------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Item ID |
| transaction_id | BIGINT UNSIGNED | NOT NULL, FK | Transaction reference |
| product_id | BIGINT UNSIGNED | NOT NULL, FK | Product sold |
| batch_id | BIGINT UNSIGNED | NULL, FK | Batch used (FIFO) |
| quantity | INT | NOT NULL | Quantity sold |
| unit_price | DECIMAL(10,2) | NOT NULL | Price per unit |
| subtotal | DECIMAL(10,2) | NOT NULL | Line total |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |

**Indexes:**
- PRIMARY KEY (id)
- INDEX (transaction_id)
- INDEX (product_id)
- INDEX (batch_id)

**Relationships:**
- belongsTo: transaction, product, product_batch

**Business Logic:**
```sql
-- Calculate subtotal
quantity * unit_price

-- Profit calculation
quantity * (unit_price - product.purchase_price)
```

---

### 6. suppliers

Vendor management.

| Column | Type | Attributes | Description |
|--------|------|-----------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Supplier ID |
| name | VARCHAR(255) | NOT NULL | Contact person |
| company_name | VARCHAR(255) | NOT NULL | Company name |
| email | VARCHAR(255) | NULL | Email address |
| phone | VARCHAR(20) | NULL | Phone number |
| address | TEXT | NULL | Address |
| city | VARCHAR(100) | NULL | City |
| country | VARCHAR(100) | DEFAULT 'Bangladesh' | Country |
| tax_id | VARCHAR(100) | NULL | Tax ID/TIN |
| notes | TEXT | NULL | Additional notes |
| is_active | BOOLEAN | DEFAULT 1 | Active status |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |
| deleted_at | TIMESTAMP | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY KEY (id)
- INDEX (is_active)
- INDEX (deleted_at)

**Relationships:**
- hasMany: products, purchase_orders

---

### 7. purchase_orders

Stock orders from suppliers.

| Column | Type | Attributes | Description |
|--------|------|-----------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | PO ID |
| po_number | VARCHAR(255) | UNIQUE, NOT NULL | PO reference |
| supplier_id | BIGINT UNSIGNED | NOT NULL, FK | Supplier reference |
| user_id | BIGINT UNSIGNED | NOT NULL, FK | Created by |
| status | ENUM | NOT NULL | PENDING/ORDERED/RECEIVED/CANCELLED |
| order_date | DATE | NOT NULL | Order date |
| expected_delivery_date | DATE | NULL | Expected delivery |
| received_date | DATE | NULL | Actual receipt date |
| subtotal | DECIMAL(10,2) | NOT NULL | Items total |
| tax | DECIMAL(10,2) | DEFAULT 0 | Tax amount |
| shipping | DECIMAL(10,2) | DEFAULT 0 | Shipping cost |
| total | DECIMAL(10,2) | NOT NULL | Grand total |
| notes | TEXT | NULL | Order notes |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |
| deleted_at | TIMESTAMP | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE (po_number)
- INDEX (supplier_id)
- INDEX (user_id)
- INDEX (status)
- INDEX (order_date)
- INDEX (deleted_at)

**Relationships:**
- belongsTo: supplier, user
- hasMany: purchase_order_items

**Business Logic:**
```sql
-- PO number format
PO-YYYYMMDD-0001

-- Status workflow
PENDING → ORDERED → RECEIVED
        ↘ CANCELLED
```

---

### 8. purchase_order_items

Line items for purchase orders.

| Column | Type | Attributes | Description |
|--------|------|-----------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Item ID |
| purchase_order_id | BIGINT UNSIGNED | NOT NULL, FK | PO reference |
| product_id | BIGINT UNSIGNED | NOT NULL, FK | Product ordered |
| quantity_ordered | INT | NOT NULL | Quantity ordered |
| quantity_received | INT | DEFAULT 0 | Quantity received |
| unit_price | DECIMAL(10,2) | NOT NULL | Cost per unit |
| subtotal | DECIMAL(10,2) | NOT NULL | Line total |
| batch_number | VARCHAR(255) | NULL | Batch number (on receipt) |
| expiry_date | DATE | NULL | Expiry date (on receipt) |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |

**Indexes:**
- PRIMARY KEY (id)
- INDEX (purchase_order_id)
- INDEX (product_id)

**Relationships:**
- belongsTo: purchase_order, product

---

### 9. customers

Customer accounts for credit management.

| Column | Type | Attributes | Description |
|--------|------|-----------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Customer ID |
| name | VARCHAR(255) | NOT NULL | Full name |
| phone | VARCHAR(20) | UNIQUE, NOT NULL | Phone number |
| email | VARCHAR(255) | NULL | Email address |
| address | TEXT | NULL | Address |
| city | VARCHAR(100) | NULL | City |
| id_number | VARCHAR(100) | NULL | National ID/Passport |
| credit_limit | DECIMAL(10,2) | DEFAULT 0 | Maximum credit |
| current_balance | DECIMAL(10,2) | DEFAULT 0 | Outstanding balance |
| credit_enabled | BOOLEAN | DEFAULT 0 | Credit allowed |
| is_active | BOOLEAN | DEFAULT 1 | Active status |
| notes | TEXT | NULL | Notes |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |
| deleted_at | TIMESTAMP | NULL | Soft delete timestamp |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE (phone)
- INDEX (email)
- INDEX (credit_enabled)
- INDEX (is_active)
- INDEX (deleted_at)

**Relationships:**
- hasMany: transactions, customer_credit_transactions

**Business Logic:**
```sql
-- Available credit
credit_limit - current_balance

-- Overdue check
current_balance > credit_limit
```

---

### 10. customer_credit_transactions

Audit trail for credit changes.

| Column | Type | Attributes | Description |
|--------|------|-----------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Record ID |
| customer_id | BIGINT UNSIGNED | NOT NULL, FK | Customer reference |
| transaction_id | BIGINT UNSIGNED | NULL, FK | Related transaction |
| user_id | BIGINT UNSIGNED | NOT NULL, FK | User who recorded |
| type | ENUM | NOT NULL | SALE/PAYMENT/ADJUSTMENT |
| amount | DECIMAL(10,2) | NOT NULL | Transaction amount |
| balance_before | DECIMAL(10,2) | NOT NULL | Balance before |
| balance_after | DECIMAL(10,2) | NOT NULL | Balance after |
| payment_method | VARCHAR(50) | NULL | Payment method (if payment) |
| notes | TEXT | NULL | Notes |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |

**Indexes:**
- PRIMARY KEY (id)
- INDEX (customer_id)
- INDEX (transaction_id)
- INDEX (type)
- INDEX (created_at)

**Relationships:**
- belongsTo: customer, transaction, user

---

### 11. dues

Simple due tracking (notebook-style).

| Column | Type | Attributes | Description |
|--------|------|-----------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Due ID |
| customer_name | VARCHAR(255) | NOT NULL | Customer name |
| customer_phone | VARCHAR(20) | NULL | Phone number |
| transaction_id | BIGINT UNSIGNED | NULL, FK | Related transaction |
| user_id | BIGINT UNSIGNED | NOT NULL, FK | Created by |
| amount | DECIMAL(10,2) | NOT NULL | Total due amount |
| amount_paid | DECIMAL(10,2) | DEFAULT 0 | Amount paid so far |
| amount_remaining | DECIMAL(10,2) | NOT NULL | Outstanding amount |
| status | ENUM | NOT NULL | PENDING/PARTIAL/PAID |
| notes | TEXT | NULL | Notes |
| due_date | DATE | NULL | Due date |
| paid_at | TIMESTAMP | NULL | Fully paid timestamp |
| created_at | TIMESTAMP | NULL | Creation timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |

**Indexes:**
- PRIMARY KEY (id)
- INDEX (customer_phone)
- INDEX (status)
- INDEX (created_at)
- INDEX (due_date)

**Relationships:**
- belongsTo: transaction, user
- hasMany: due_payments

**Business Logic:**
```sql
-- Status logic
PENDING: amount_paid = 0
PARTIAL: 0 < amount_paid < amount
PAID: amount_paid = amount

-- Overdue check
due_date < CURDATE() AND status != 'PAID'
```

---

### 12. due_payments

Payment records for dues.

| Column | Type | Attributes | Description |
|--------|------|-----------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Payment ID |
| due_id | BIGINT UNSIGNED | NOT NULL, FK | Due reference |
| user_id | BIGINT UNSIGNED | NOT NULL, FK | Recorded by |
| amount | DECIMAL(10,2) | NOT NULL | Payment amount |
| payment_method | VARCHAR(50) | NOT NULL | CASH/CARD/MOBILE/OTHER |
| notes | TEXT | NULL | Payment notes |
| created_at | TIMESTAMP | NULL | Payment timestamp |
| updated_at | TIMESTAMP | NULL | Last update timestamp |

**Indexes:**
- PRIMARY KEY (id)
- INDEX (due_id)
- INDEX (created_at)

**Relationships:**
- belongsTo: due, user

---

## Relationships

### One-to-Many

```sql
-- User relationships
users.id → transactions.user_id
users.id → purchase_orders.user_id
users.id → dues.user_id
users.id → due_payments.user_id

-- Product relationships
products.id → product_batches.product_id
products.id → transaction_items.product_id
products.id → purchase_order_items.product_id

-- Supplier relationships
suppliers.id → products.supplier_id
suppliers.id → purchase_orders.supplier_id

-- Transaction relationships
transactions.id → transaction_items.transaction_id
transactions.id → customer_credit_transactions.transaction_id
transactions.id → dues.transaction_id

-- Customer relationships
customers.id → transactions.customer_id
customers.id → customer_credit_transactions.customer_id

-- Purchase Order relationships
purchase_orders.id → purchase_order_items.purchase_order_id

-- Due relationships
dues.id → due_payments.due_id
```

### Self-Referencing

```sql
-- Transaction returns
transactions.id → transactions.return_for_transaction_id
```

---

## Indexes

### Performance Indexes

All foreign keys are indexed automatically. Additional indexes for query optimization:

**Search Indexes:**
```sql
products (generic_name, brand_name, barcode)
customers (phone, email)
dues (customer_phone)
```

**Date Indexes:**
```sql
transactions (created_at)
product_batches (expiry_date)
purchase_orders (order_date, expected_delivery_date)
dues (due_date)
```

**Status Indexes:**
```sql
products (is_active)
users (role, is_active)
purchase_orders (status)
dues (status)
```

---

## Migrations

### Migration Order

1. `create_users_table`
2. `create_suppliers_table`
3. `create_products_table`
4. `create_product_batches_table`
5. `create_customers_table`
6. `create_transactions_table`
7. `create_transaction_items_table`
8. `create_customer_credit_transactions_table`
9. `create_purchase_orders_table`
10. `create_purchase_order_items_table`
11. `create_dues_table`
12. `create_due_payments_table`

### Running Migrations

```bash
# Run all migrations
php artisan migrate

# Rollback last batch
php artisan migrate:rollback

# Rollback all and re-run
php artisan migrate:fresh

# With seeding
php artisan migrate:fresh --seed
```

---

**Document Version:** 1.0
**Database Version:** 2.6.0
**Last Updated:** November 2025

---

*End of Database Schema Documentation*

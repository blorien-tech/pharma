# Database Schema Documentation

## BLORIEN Pharma - Complete Database Structure

Last Updated: January 2025 (Post Phase 3A)

---

## Overview

- **Total Tables**: 12
- **Database Engine**: MySQL 8.0 / InnoDB
- **Character Set**: utf8mb4_unicode_ci
- **Key Features**: Soft deletes, Timestamps, Foreign key constraints, Indexes

---

## Table of Contents

1. [Core Tables](#core-tables)
   - users
   - products
   - product_batches
2. [Transaction Tables](#transaction-tables)
   - transactions
   - transaction_items
3. [Supply Chain Tables](#supply-chain-tables)
   - suppliers
   - purchase_orders
   - purchase_order_items
4. [Customer Tables](#customer-tables)
   - customers
   - customer_credit_transactions
5. [Due Tracking Tables](#due-tracking-tables)
   - dues (Phase 3A)
   - due_payments (Phase 3A)

---

## Core Tables

### 1. users

**Purpose**: System user authentication and role management

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('owner', 'manager', 'cashier') DEFAULT 'cashier',
    is_active BOOLEAN DEFAULT TRUE,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);
```

**Indexes**:

- PRIMARY KEY (id)
- UNIQUE (email)
- INDEX (role)
- INDEX (is_active)
- INDEX (deleted_at)

**Roles**:

- `owner`: Full system access
- `manager`: Product, inventory, reports, users
- `cashier`: POS and product viewing only

---

### 2. products

**Purpose**: Medicine/product catalog with generic/brand name support

```sql
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    generic_name VARCHAR(255) NULL,              -- Phase 3A
    brand_name VARCHAR(255) NULL,                -- Phase 3A
    sku VARCHAR(255) NOT NULL UNIQUE,
    barcode VARCHAR(255) NULL UNIQUE,            -- Phase 3A
    supplier_id BIGINT UNSIGNED NULL,
    description TEXT NULL,
    purchase_price DECIMAL(12,2) NOT NULL,
    selling_price DECIMAL(12,2) NOT NULL,
    current_stock INT NOT NULL DEFAULT 0,
    min_stock INT NOT NULL DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
);
```

**Indexes**:

- PRIMARY KEY (id)
- UNIQUE (sku)
- UNIQUE (barcode)
- INDEX (generic_name) -- Phase 3A: Fast search by generic name
- INDEX (brand_name) -- Phase 3A: Fast search by brand name
- INDEX (supplier_id)
- INDEX (is_active)
- INDEX (current_stock)
- INDEX (deleted_at)

**Search Strategy**: Multi-field OR search on name, generic_name, brand_name, sku, barcode, description

---

### 3. product_batches

**Purpose**: Batch tracking with expiry dates (FIFO inventory)

```sql
CREATE TABLE product_batches (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    batch_number VARCHAR(255) NOT NULL,
    expiry_date DATE NOT NULL,
    quantity_received INT NOT NULL,
    quantity_remaining INT NOT NULL,
    purchase_price DECIMAL(12,2) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
```

**Indexes**:

- PRIMARY KEY (id)
- INDEX (product_id)
- INDEX (expiry_date)
- INDEX (is_active)

**Status Logic**:

- Active: quantity_remaining > 0 AND expiry_date > today
- Expiring Soon: expiry_date <= today + 30 days
- Expired: expiry_date < today
- Depleted: quantity_remaining = 0

---

## Transaction Tables

### 4. transactions

**Purpose**: Sales and returns recording with customer linking

```sql
CREATE TABLE transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    receipt_number VARCHAR(255) NOT NULL UNIQUE,
    user_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NULL,            -- Phase 2
    type ENUM('SALE', 'RETURN') DEFAULT 'SALE',
    subtotal DECIMAL(12,2) NOT NULL,
    discount DECIMAL(12,2) DEFAULT 0,
    total DECIMAL(12,2) NOT NULL,
    payment_method ENUM('CASH', 'CARD', 'MOBILE', 'CREDIT', 'OTHER') DEFAULT 'CASH',
    amount_paid DECIMAL(12,2) NOT NULL,
    change_amount DECIMAL(12,2) DEFAULT 0,
    is_credit BOOLEAN DEFAULT FALSE,             -- Phase 2
    return_for_transaction_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (return_for_transaction_id) REFERENCES transactions(id) ON DELETE SET NULL
);
```

**Indexes**:

- PRIMARY KEY (id)
- UNIQUE (receipt_number)
- INDEX (user_id)
- INDEX (customer_id)
- INDEX (type)
- INDEX (payment_method)
- INDEX (created_at)

**Receipt Number Format**: TXN-{YYYYMMDDHHIISS}-{RANDOM}

---

### 5. transaction_items

**Purpose**: Line items for each transaction (sold products)

```sql
CREATE TABLE transaction_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    product_batch_id BIGINT UNSIGNED NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    FOREIGN KEY (product_batch_id) REFERENCES product_batches(id) ON DELETE SET NULL
);
```

**Indexes**:

- PRIMARY KEY (id)
- INDEX (transaction_id)
- INDEX (product_id)
- INDEX (product_batch_id)

---

## Supply Chain Tables

### 6. suppliers

**Purpose**: Supplier/vendor information management

```sql
CREATE TABLE suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    supplier_name VARCHAR(255) NOT NULL,
    company_name VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    tax_id VARCHAR(100) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);
```

**Indexes**:

- PRIMARY KEY (id)
- INDEX (is_active)
- INDEX (deleted_at)

---

### 7. purchase_orders

**Purpose**: Stock purchase orders from suppliers

```sql
CREATE TABLE purchase_orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    po_number VARCHAR(255) NOT NULL UNIQUE,
    supplier_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    order_date DATE NOT NULL,
    expected_delivery DATE NULL,
    received_date DATE NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    shipping_cost DECIMAL(12,2) DEFAULT 0,
    tax DECIMAL(12,2) DEFAULT 0,
    total DECIMAL(12,2) NOT NULL,
    status ENUM('PENDING', 'ORDERED', 'RECEIVED', 'CANCELLED') DEFAULT 'PENDING',
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**Indexes**:

- PRIMARY KEY (id)
- UNIQUE (po_number)
- INDEX (supplier_id)
- INDEX (user_id)
- INDEX (status)
- INDEX (order_date)

**PO Number Format**: PO-{YYYYMMDD}-{RANDOM}

---

### 8. purchase_order_items

**Purpose**: Line items in purchase orders

```sql
CREATE TABLE purchase_order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    purchase_order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity_ordered INT NOT NULL,
    quantity_received INT DEFAULT 0,
    unit_price DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    batch_number VARCHAR(255) NULL,
    expiry_date DATE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);
```

**Indexes**:

- PRIMARY KEY (id)
- INDEX (purchase_order_id)
- INDEX (product_id)

---

## Customer Tables

### 9. customers

**Purpose**: Customer accounts with credit management and phone-based tracking

```sql
CREATE TABLE customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL UNIQUE,           -- Phase 3A: Made UNIQUE
    email VARCHAR(255) NULL,
    address TEXT NULL,
    city VARCHAR(100) NULL,
    id_number VARCHAR(50) NULL,
    credit_limit DECIMAL(12,2) DEFAULT 0,
    current_balance DECIMAL(12,2) DEFAULT 0,
    credit_enabled BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);
```

**Indexes**:

- PRIMARY KEY (id)
- UNIQUE (phone) -- Phase 3A: Quick customer lookup
- INDEX (email)
- INDEX (credit_enabled)
- INDEX (is_active)
- INDEX (deleted_at)

**Credit Logic**:

- Available Credit = credit_limit - current_balance
- Overdue = current_balance > credit_limit

---

### 10. customer_credit_transactions

**Purpose**: Audit trail for all customer credit operations

```sql
CREATE TABLE customer_credit_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id BIGINT UNSIGNED NOT NULL,
    transaction_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    type ENUM('SALE', 'PAYMENT', 'ADJUSTMENT') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    balance_before DECIMAL(12,2) NOT NULL,
    balance_after DECIMAL(12,2) NOT NULL,
    payment_method ENUM('CASH', 'CARD', 'MOBILE', 'OTHER') NULL,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**Indexes**:

- PRIMARY KEY (id)
- INDEX (customer_id)
- INDEX (transaction_id)
- INDEX (type)
- INDEX (created_at)

---

## Due Tracking Tables

### 11. dues

**Purpose**: Simple notebook-style due tracking

```sql
CREATE TABLE dues (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NULL,
    transaction_id BIGINT UNSIGNED NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    amount_paid DECIMAL(12,2) DEFAULT 0,
    amount_remaining DECIMAL(12,2) NOT NULL,
    status ENUM('PENDING', 'PARTIAL', 'PAID') DEFAULT 'PENDING',
    notes TEXT NULL,
    due_date DATE NULL,
    paid_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**Indexes**:

- PRIMARY KEY (id)
- INDEX (customer_phone)
- INDEX (status)
- INDEX (created_at)
- INDEX (due_date)

**Key Features**:

- No forced customer account linking
- Quick entry with just name (phone optional)
- Tracks partial payments
- Auto status updates: PENDING → PARTIAL → PAID

**Status Auto-Update Logic**:

```bash
amount_remaining = 0 → PAID (set paid_at)
amount_remaining > 0 AND amount_paid > 0 → PARTIAL
amount_paid = 0 → PENDING
```

---

### 12. due_payments

**Purpose**: Track partial payments against dues

```sql
CREATE TABLE due_payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    due_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    payment_method ENUM('CASH', 'CARD', 'MOBILE', 'OTHER') DEFAULT 'CASH',
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (due_id) REFERENCES dues(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**Indexes**:

- PRIMARY KEY (id)
- INDEX (due_id)
- INDEX (created_at)

---

## Database Relationships

### Entity Relationship Diagram (ERD)

```bash
users (1) ─────── (*) transactions
users (1) ─────── (*) purchase_orders
users (1) ─────── (*) customer_credit_transactions
users (1) ─────── (*) dues
users (1) ─────── (*) due_payments

products (1) ──── (*) product_batches
products (1) ──── (*) transaction_items
products (1) ──── (*) purchase_order_items
products (*) ──── (1) suppliers [optional]

transactions (1) ─ (*) transaction_items
transactions (1) ─ (0..1) transactions [return_for]
transactions (*) ─ (0..1) customers [optional]
transactions (0..1) ─ (*) dues [optional]

suppliers (1) ──── (*) purchase_orders

purchase_orders (1) ─ (*) purchase_order_items

customers (1) ──── (*) customer_credit_transactions

dues (1) ────────── (*) due_payments
```

---

## Database Size Estimates

### Expected Storage (Small Pharmacy - 1 Year)

| Table                        | Estimated Rows | Storage |
| ---------------------------- | -------------- | ------- |
| users                        | 3-5            | ~1 KB   |
| products                     | 500-2000       | ~500 KB |
| product_batches              | 2000-5000      | ~2 MB   |
| transactions                 | 10,000-50,000  | ~10 MB  |
| transaction_items            | 30,000-150,000 | ~20 MB  |
| suppliers                    | 10-50          | ~10 KB  |
| purchase_orders              | 500-2000       | ~500 KB |
| purchase_order_items         | 2000-10,000    | ~2 MB   |
| customers                    | 100-500        | ~100 KB |
| customer_credit_transactions | 500-5000       | ~1 MB   |
| dues                         | 1000-5000      | ~1 MB   |
| due_payments                 | 2000-10,000    | ~1 MB   |

**Total Estimated**: ~40-60 MB per year

---

## Backup Strategy

### Recommended Approach

```bash
# Daily backup
mysqldump -u root -p blorien_pharma > backup_$(date +%Y%m%d).sql

# Weekly full backup with compression
mysqldump -u root -p blorien_pharma | gzip > backup_$(date +%Y%m%d).sql.gz

# Retention: 7 daily, 4 weekly, 12 monthly
```

---

## Migration History

1. **Phase 1 (MVP)**: Tables 1-5 (users, products, batches, transactions)
2. **Phase 2**: Tables 6-10 (suppliers, POs, customers, credit)
3. **Phase 3A**: Updated customers.phone (UNIQUE), products (generic/brand/barcode), Tables 11-12 (dues)

---

## Query Performance Tips

### Frequently Used Queries

- **Product Search (with generic/brand)**

```sql
-- Optimized with indexes on name, generic_name, brand_name, sku, barcode
SELECT * FROM products
WHERE (name LIKE '%search%'
   OR generic_name LIKE '%search%'
   OR brand_name LIKE '%search%')
  AND is_active = 1
  AND deleted_at IS NULL
LIMIT 20;
```

- **Customer Lookup by Phone**

```sql
-- UNIQUE index on phone makes this very fast
SELECT * FROM customers
WHERE phone = '01712345678'
  AND is_active = 1
  AND deleted_at IS NULL;
```

- **Overdue Dues**

```sql
-- Compound index on (status, due_date)
SELECT * FROM dues
WHERE status != 'PAID'
  AND due_date < CURDATE()
ORDER BY due_date ASC;
```

- **Low Stock Alert**

```sql
-- Index on current_stock
SELECT * FROM products
WHERE current_stock <= min_stock
  AND is_active = 1
  AND deleted_at IS NULL;
```

- **Expiring Batches**

```sql
-- Index on expiry_date
SELECT pb.*, p.name
FROM product_batches pb
JOIN products p ON pb.product_id = p.id
WHERE pb.expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
  AND pb.quantity_remaining > 0
  AND pb.is_active = 1;
```

---

## Data Integrity Rules

### Cascading Deletes

- Delete Product → Cascade to batches
- Delete Transaction → Cascade to transaction_items
- Delete Customer → Cascade to credit_transactions
- Delete Due → Cascade to due_payments
- Delete PO → Cascade to PO items

### Soft Deletes (Paranoid Delete)

- users
- products
- suppliers
- customers

### Restricted Deletes

- Cannot delete product if in transaction_items
- Cannot delete product if in purchase_order_items

---

## Database Migrations

Located in: `app/database/migrations/`

**Run migrations:**

```bash
docker compose exec app php artisan migrate
```

**Rollback last migration:**

```bash
docker compose exec app php artisan migrate:rollback
```

**Fresh migration (DEV ONLY - destroys data):**

```bash
docker compose exec app php artisan migrate:fresh
```

---

**Last Updated**: Phase 3A Complete - January 2025
**Total Tables**: 12
**Total Indexes**: 60+
**Foreign Keys**: 25+

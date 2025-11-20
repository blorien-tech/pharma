# BLORIEN PHARMA - ARCHITECTURE SUMMARY

## System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────────┐
│                         Laravel Application                          │
│                      BLORIEN Pharma System v1.0                      │
└─────────────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────────────┐
│                      REQUEST LAYER (Routes)                        │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│  WEB ROUTES (/routes/web.php)      API ROUTES (/routes/api.php)  │
│  ├─ Authentication                 ├─ Products                   │
│  ├─ Dashboard                       ├─ Transactions              │
│  ├─ Products CRUD                   ├─ Batches                   │
│  ├─ POS Interface                   ├─ Users                     │
│  ├─ Suppliers CRUD                  ├─ Analytics Data            │
│  ├─ Purchase Orders                 └─ Dashboard Stats           │
│  ├─ Customers                                                    │
│  ├─ Reports                                                      │
│  └─ Analytics                                                    │
│                                                                  │
│  Middleware: auth, role:owner,manager                           │
└────────────────────────────────────────────────────────────────────┘
          ↓
┌────────────────────────────────────────────────────────────────────┐
│                   CONTROLLER LAYER (12 Controllers)                │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│  • AuthController          • BatchController                      │
│  • DashboardController     • UserController                       │
│  • ProductController       • ReportController                     │
│  • PosController           • AnalyticsController                  │
│  • TransactionController   • SupplierController                   │
│  • CustomerController      • PurchaseOrderController              │
│                                                                    │
│  Role: Request validation, call services, return responses       │
└────────────────────────────────────────────────────────────────────┘
          ↓
┌────────────────────────────────────────────────────────────────────┐
│                    SERVICE LAYER (3 Services)                      │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│  ┌─────────────────┐  ┌──────────────────┐  ┌───────────────┐   │
│  │   PosService    │  │InventoryService  │  │PurchaseOrder  │   │
│  ├─────────────────┤  ├──────────────────┤  │    Service    │   │
│  │• processSale()  │  │• getLowStock()   │  ├───────────────┤   │
│  │• processReturn()│  │• getExpiring()   │  │• create()     │   │
│  │• validateCredit │  │• getExpired()    │  │• receiveStock │   │
│  │• updateBalance  │  │• getBestBatch()  │  │• markOrdered()│   │
│  │                 │  │• getAllAlerts()  │  │               │   │
│  │ DB Transaction  │  │• deductStock()   │  │ DB Transaction│   │
│  │ Audit Trail     │  │• addStock()      │  │ Auto ID Gen   │   │
│  └─────────────────┘  └──────────────────┘  └───────────────┘   │
│                                                                    │
│  Role: Business logic, data validation, DB transactions          │
└────────────────────────────────────────────────────────────────────┘
          ↓
┌────────────────────────────────────────────────────────────────────┐
│                    MODEL LAYER (10 Models)                         │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│  Users ─────────┐                                                 │
│  Products ──┐   │   Transactions                                  │
│  Batches ───┼───┼───┬─ TransactionItems                           │
│  Suppliers──┘   │   └─ Returns                                    │
│  POs ───────────┼───┬─ PO Items                                   │
│  Customers ─────┼───┴─ CreditTransactions                         │
│                 │                                                 │
│  Role: Data structure, relationships, query scopes, validation   │
└────────────────────────────────────────────────────────────────────┘
          ↓
┌────────────────────────────────────────────────────────────────────┐
│                    DATABASE LAYER (MySQL)                          │
├────────────────────────────────────────────────────────────────────┤
│                                                                    │
│  Core Tables:                                                     │
│  ├─ users (3 roles: owner, manager, cashier)                     │
│  ├─ products (with supplier relation)                            │
│  ├─ product_batches (FIFO by expiry_date)                       │
│  ├─ transactions (SALE/RETURN)                                   │
│  ├─ transaction_items (line items)                               │
│  ├─ suppliers (with PO tracking)                                 │
│  ├─ purchase_orders (4-status workflow)                          │
│  ├─ purchase_order_items (line items)                            │
│  ├─ customers (with credit management)                           │
│  └─ customer_credit_transactions (audit trail)                   │
│                                                                    │
│  + System tables: password_reset_tokens, sessions                │
│  + Soft Deletes: products, suppliers, POs, customers             │
│  + Indexes: 50+ for query optimization                           │
└────────────────────────────────────────────────────────────────────┘
```

---

## Data Flow Diagrams

### Sales Transaction Flow

```
┌──────────────────┐
│  Customer Sale   │
└────────┬─────────┘
         ↓
┌────────────────────────────┐
│ POS Interface              │
│ • Search Products          │
│ • Select Batch (FIFO)      │
│ • Add to Cart              │
│ • Set Payment Method       │
│ • Apply Discount           │
└────────┬───────────────────┘
         ↓
┌────────────────────────────┐
│ POST /api/transactions     │
│ TransactionController      │
└────────┬───────────────────┘
         ↓
┌────────────────────────────┐
│ PosService::processSale()  │
│ • Validate Stock           │
│ • Select Best Batch (FIFO) │
│ • Calculate Total          │
│ • Check Credit (if needed) │
│ DB::transaction() {        │
│   Create Transaction       │
│   Create TransactionItems  │
│   Deduct Stock             │
│   Update Customer Balance  │
│   Create CreditTransaction │
│ }                          │
└────────┬───────────────────┘
         ↓
┌────────────────────────────┐
│ Database Updates           │
│ ├─ transactions (new)      │
│ ├─ transaction_items (new) │
│ ├─ products.current_stock  │
│ ├─ product_batches.qty_rem │
│ ├─ customers.balance (±)   │
│ └─ credit_transactions (new)
└────────┬───────────────────┘
         ↓
┌────────────────────────────┐
│ Success Response           │
│ Transaction ID             │
│ Receipt Data               │
└────────────────────────────┘
```

### Purchase Order Workflow

```
┌──────────────────────┐
│ Create Purchase Order│
│ (Owner/Manager)      │
└─────────┬────────────┘
          ↓
┌──────────────────────────────┐
│ POST /purchase-orders        │
│ Select Supplier & Items      │
│ Set Order Date, Tax, Shipping│
└─────────┬────────────────────┘
          ↓
┌──────────────────────────────┐
│ PurchaseOrderService::       │
│ createPurchaseOrder()        │
│ • Generate PO Number         │
│ • Calculate Total            │
│ • Create PurchaseOrder       │
│ • Create PurchaseOrderItems  │
│ Status: PENDING              │
└─────────┬────────────────────┘
          ↓
┌──────────────────────────────┐
│ PO Created                   │
│ View PO Details              │
└─────────┬────────────────────┘
          ↓
┌──────────────────────────────┐
│ [Optional] Mark as Ordered   │
│ Status: ORDERED              │
└─────────┬────────────────────┘
          ↓
┌──────────────────────────────┐
│ Receive Stock Form           │
│ For each item:               │
│ • Qty Received               │
│ • Batch Number               │
│ • Expiry Date                │
└─────────┬────────────────────┘
          ↓
┌──────────────────────────────┐
│ POST /purchase-orders/:id/   │
│ receive                      │
│ PurchaseOrderService::       │
│ receiveStock()               │
│ DB::transaction() {          │
│   Update PurchaseOrder       │
│   Update Items (qty_recv)    │
│   Create ProductBatches      │
│   Update Product.stock       │
│   Update Product.price       │
│ }                            │
│ Status: RECEIVED             │
└─────────┬────────────────────┘
          ↓
┌──────────────────────────────┐
│ Inventory Updated            │
│ Stock Available              │
│ Ready for Sales              │
└──────────────────────────────┘
```

### Customer Credit Flow

```
┌──────────────────────────┐
│ Create Customer          │
│ Set Credit Limit         │
│ Enable Credit            │
└────────┬─────────────────┘
         ↓
┌──────────────────────────┐
│ Credit Sale Transaction  │
│ POS with customer select │
└────────┬─────────────────┘
         ↓
┌──────────────────────────────────┐
│ PosService::processSale()        │
│ • Validate credit_enabled        │
│ • Check availableCredit >= total │
│ • Create Transaction (is_credit) │
│ • Create CreditTransaction       │
│   (type: SALE)                   │
│ • Increment customer.balance     │
│ DB::transaction()                │
└────────┬─────────────────────────┘
         ↓
┌──────────────────────────────────┐
│ Customer Balance Updated         │
│ current_balance += sale amount   │
│ Audit trail created              │
└────────┬─────────────────────────┘
         ↓
┌──────────────────────────────────┐
│ Customer Payment                 │
│ View: /customers/:id/payment     │
│ Enter Payment Amount              │
└────────┬─────────────────────────┘
         ↓
┌──────────────────────────────────┐
│ POST /customers/:id/payment      │
│ recordPayment()                  │
│ DB::transaction() {              │
│   Create CreditTransaction       │
│   (type: PAYMENT)                │
│   Decrement customer.balance     │
│ }                                │
└────────┬─────────────────────────┘
         ↓
┌──────────────────────────────────┐
│ Payment Recorded                 │
│ Balance Updated                  │
│ Audit Entry Created              │
└──────────────────────────────────┘
```

---

## File Organization

```
pharma/
├── app/
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/ (12 controllers)
│   │   ├── Models/ (10 models)
│   │   └── Services/ (3 services)
│   ├── database/
│   │   └── migrations/ (8 migrations)
│   ├── resources/
│   │   └── views/ (38 blade templates)
│   ├── routes/
│   │   ├── web.php (50 routes)
│   │   └── api.php (45 endpoints)
│   └── config/
│       └── [Laravel config files]
├── SYSTEM_REVIEW.md (this comprehensive report)
└── ARCHITECTURE_SUMMARY.md (this file)
```

---

## Key Design Patterns

### 1. Service Layer Pattern
- Business logic in dedicated services
- Controllers delegate to services
- Reusable, testable code

### 2. FIFO Inventory Management
- Products tracked at batch level
- Ordered by expiry_date (ascending)
- Prevents waste, ensures compliance

### 3. Audit Trail Pattern
- Customer credit transactions recorded
- Returns use negative values
- User attribution on all actions
- Immutable transaction records

### 4. Role-Based Access Control
- 3 roles: owner, manager, cashier
- Middleware-based enforcement
- Route-level permissions

### 5. Database Transaction Safety
- Multi-step operations in transactions
- ACID compliance
- Automatic rollback on error

### 6. Soft Delete Pattern
- Data preservation
- Logical deletion (not physical)
- Applied to: products, suppliers, POs, customers

---

## Key Metrics

### Database
- 10 core tables
- 50+ indexed columns
- FIFO ordering by expiry date
- Cascading deletes for integrity
- Soft deletes for audit trail

### Code
- 12 controllers
- 3 services
- 10 models
- 38 views
- 95 total routes (web + API)

### Features
- 8 CRUD resources
- 6 report types
- 4 payment methods
- 3 user roles
- 2 transaction types

---

## Performance Considerations

### Indexes
- Primary keys indexed
- Foreign keys indexed
- Search fields indexed (sku, email, phone, name)
- Status columns indexed (for filtering)
- Date columns indexed (for range queries)

### Query Optimization
- Eager loading relationships
- Model scopes for common filters
- Pagination on list views
- Database transactions for consistency

### Caching Opportunities
- Dashboard stats (refresh interval)
- Product lists for POS
- Supplier dropdown lists
- Batch expiry alerts (daily)

---

## Security Features

### Authentication
- Email + password login
- Session-based auth
- Remember tokens
- Active status validation
- Password hashing (bcrypt)

### Authorization
- Role-based access control
- Route middleware protection
- Controller-level checks
- Soft delete protection

### Data Protection
- Mass assignment fillable arrays
- Input validation on all forms
- Database transactions for consistency
- Soft deletes prevent accidental data loss

---

## Scalability Roadmap

### Current Capacity
- Single-user (1 store)
- 10K+ products
- 100K+ transactions per year
- Unlimited customers

### Future Enhancements
1. Multi-location support (warehouse management)
2. Advanced analytics (ML-based forecasting)
3. Mobile app (dedicated POS app)
4. API versioning (v1, v2, v3)
5. Caching layer (Redis)
6. Queue system (batch operations)
7. Real-time notifications (WebSockets)
8. Detailed audit logs (separate table)

---

**Generated:** November 19, 2025  
**System Version:** 1.0  
**Laravel Version:** Latest (as per composer.json)  
**Database:** MySQL/MariaDB

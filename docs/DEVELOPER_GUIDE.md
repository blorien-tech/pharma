# BLORIEN Pharma - Developer Guide

**Version:** 2.6.0
**Audience:** Software Developers, DevOps Engineers
**Last Updated:** November 2025

---

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Technology Stack](#technology-stack)
3. [Project Structure](#project-structure)
4. [Development Setup](#development-setup)
5. [Coding Standards](#coding-standards)
6. [Database Design](#database-design)
7. [Service Layer](#service-layer)
8. [Frontend Architecture](#frontend-architecture)
9. [Testing](#testing)
10. [Deployment](#deployment)
11. [Contributing](#contributing)

---

## Architecture Overview

### System Architecture

BLORIEN Pharma follows a **layered MVC architecture** with an additional service layer for business logic:

```
┌─────────────────────────────────────────┐
│         Presentation Layer              │
│   (Blade Templates + Alpine.js)         │
└────────────────┬────────────────────────┘
                 │
┌────────────────▼────────────────────────┐
│         Controller Layer                │
│      (16 HTTP Controllers)              │
└────────────────┬────────────────────────┘
                 │
┌────────────────▼────────────────────────┐
│         Service Layer                   │
│  (PosService, InventoryService, etc)    │
└────────────────┬────────────────────────┘
                 │
┌────────────────▼────────────────────────┐
│          Model Layer                    │
│    (12 Eloquent Models)                 │
└────────────────┬────────────────────────┘
                 │
┌────────────────▼────────────────────────┐
│        Database Layer                   │
│         (MySQL 8.0)                     │
└─────────────────────────────────────────┘
```

### Design Patterns

1. **MVC Pattern** - Separation of concerns
2. **Service Layer Pattern** - Business logic isolation
3. **Repository Pattern** - Data access through Eloquent ORM
4. **Observer Pattern** - Model events and listeners
5. **Factory Pattern** - Model factories for testing
6. **FIFO Pattern** - Inventory management

### Key Principles

- **DRY (Don't Repeat Yourself)** - Reusable service methods
- **SOLID Principles** - Clean, maintainable code
- **Convention over Configuration** - Laravel standards
- **Separation of Concerns** - Clear layer boundaries
- **Database Transaction Safety** - ACID compliance

---

## Technology Stack

### Backend

| Component | Technology | Version | Purpose |
|-----------|-----------|---------|---------|
| **Framework** | Laravel | 12.0 | PHP framework |
| **Language** | PHP | 8.4 | Server-side language |
| **Database** | MySQL | 8.0 | Data persistence |
| **ORM** | Eloquent | 12.0 | Database abstraction |
| **Authentication** | Laravel Auth | 12.0 | User authentication |
| **Validation** | Laravel Validator | 12.0 | Input validation |

### Frontend

| Component | Technology | Version | Purpose |
|-----------|-----------|---------|---------|
| **CSS Framework** | Tailwind CSS | 3.3.6 | Styling |
| **JS Framework** | Alpine.js | 3.x | Reactive components |
| **Build Tool** | Vite | 5.0.8 | Asset bundling |
| **Charts** | Chart.js | 4.4.0 | Data visualization |
| **HTTP Client** | Axios | 1.6.2 | AJAX requests |

### Infrastructure

| Component | Technology | Version | Purpose |
|-----------|-----------|---------|---------|
| **Container** | Docker | 20.10+ | Containerization |
| **Web Server** | Nginx | Latest | HTTP server |
| **App Server** | PHP-FPM | 8.4 | PHP processing |
| **Node.js** | Node.js | 22.x | Build tools |
| **Composer** | Composer | 2.x | PHP dependencies |

---

## Project Structure

```
pharma/
├── app/
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/     # 16 Controllers
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   ├── BatchController.php
│   │   │   │   ├── PosController.php
│   │   │   │   ├── TransactionController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── SupplierController.php
│   │   │   │   ├── PurchaseOrderController.php
│   │   │   │   ├── CustomerController.php
│   │   │   │   ├── DueController.php
│   │   │   │   ├── DailyClosingController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   ├── AnalyticsController.php
│   │   │   │   └── LanguageController.php
│   │   │   ├── Middleware/      # Custom Middleware
│   │   │   │   ├── CheckRole.php
│   │   │   │   └── SetLocale.php
│   │   │   └── Requests/        # Form Requests (add as needed)
│   │   ├── Models/              # 12 Eloquent Models
│   │   │   ├── User.php
│   │   │   ├── Product.php
│   │   │   ├── ProductBatch.php
│   │   │   ├── Transaction.php
│   │   │   ├── TransactionItem.php
│   │   │   ├── Supplier.php
│   │   │   ├── PurchaseOrder.php
│   │   │   ├── PurchaseOrderItem.php
│   │   │   ├── Customer.php
│   │   │   ├── CustomerCreditTransaction.php
│   │   │   ├── Due.php
│   │   │   └── DuePayment.php
│   │   ├── Services/            # Business Logic
│   │   │   ├── PosService.php
│   │   │   ├── InventoryService.php
│   │   │   └── PurchaseOrderService.php
│   │   └── Providers/           # Service Providers
│   ├── config/                  # Configuration
│   ├── database/
│   │   ├── migrations/          # 12 Migrations
│   │   ├── seeders/             # Database Seeders
│   │   └── factories/           # Model Factories
│   ├── resources/
│   │   ├── views/               # 42 Blade Templates
│   │   │   ├── layouts/
│   │   │   │   ├── app.blade.php
│   │   │   │   ├── sidebar.blade.php
│   │   │   │   └── topbar.blade.php
│   │   │   ├── auth/
│   │   │   ├── dashboard/
│   │   │   ├── pos/
│   │   │   ├── products/
│   │   │   ├── transactions/
│   │   │   ├── customers/
│   │   │   ├── suppliers/
│   │   │   ├── purchase-orders/
│   │   │   ├── reports/
│   │   │   ├── analytics/
│   │   │   ├── dues/
│   │   │   ├── daily-closing/
│   │   │   └── users/
│   │   ├── css/
│   │   │   └── app.css          # Tailwind directives
│   │   └── js/
│   │       ├── app.js           # Main JS entry
│   │       └── bootstrap.js     # Axios config
│   ├── routes/
│   │   ├── web.php              # Web routes (70+)
│   │   └── api.php              # API routes (30+)
│   ├── lang/                    # Translations
│   │   ├── en/                  # English
│   │   └── bn/                  # Bengali
│   ├── public/
│   │   ├── index.php            # Entry point
│   │   └── build/               # Built assets (Vite)
│   ├── storage/                 # Logs, cache, sessions
│   ├── tests/                   # Tests
│   ├── .env.example             # Environment template
│   ├── composer.json            # PHP dependencies
│   ├── package.json             # Node dependencies
│   ├── vite.config.js           # Vite configuration
│   ├── tailwind.config.js       # Tailwind configuration
│   └── postcss.config.js        # PostCSS configuration
├── docker/
│   ├── nginx/
│   │   └── Dockerfile
│   ├── php/
│   │   └── Dockerfile
│   └── mysql/
│       └── Dockerfile
├── docker-compose.yml
└── docs/                        # Documentation
```

---

## Development Setup

### Prerequisites

- Docker 20.10+
- Docker Compose 1.29+
- Git
- Text editor (VS Code recommended)

### Initial Setup

```bash
# 1. Clone repository
git clone https://github.com/blorien-tech/pharma.git
cd pharma

# 2. Start Docker containers
docker-compose up -d

# 3. Enter PHP container
docker-compose exec app bash

# 4. Install PHP dependencies
composer install

# 5. Copy environment file
cp .env.example .env

# 6. Generate application key
php artisan key:generate

# 7. Run migrations
php artisan migrate

# 8. (Optional) Seed database
php artisan db:seed

# 9. Exit container
exit

# 10. Install Node dependencies
npm install

# 11. Build assets
npm run build
# OR for development with hot reload:
npm run dev

# 12. Access application
# http://localhost:8000
```

### Environment Configuration

Edit `.env` file:

```env
APP_NAME="BLORIEN Pharma"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=blorien_pharma
DB_USERNAME=blorien_user
DB_PASSWORD=blorien_pass

# Pharmacy Settings
PHARMACY_NAME="Your Pharmacy"
PHARMACY_ADDRESS="123 Main St"
PHARMACY_PHONE="+880-XXX-XXXX"

# Inventory Settings
LOW_STOCK_THRESHOLD=10
EXPIRY_WARNING_DAYS=30
```

### Development Workflow

```bash
# Start dev server (Vite hot reload)
npm run dev

# Watch for file changes
docker-compose logs -f app

# Run migrations
docker-compose exec app php artisan migrate

# Rollback migrations
docker-compose exec app php artisan migrate:rollback

# Fresh migration
docker-compose exec app php artisan migrate:fresh

# Clear cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear

# Generate IDE helper (for autocomplete)
docker-compose exec app php artisan ide-helper:generate
```

---

## Coding Standards

### PHP Coding Standards

Follow **PSR-12** coding style:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PosService;

class TransactionController extends Controller
{
    protected PosService $posService;

    public function __construct(PosService $posService)
    {
        $this->posService = $posService;
    }

    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'payment_method' => 'required|in:CASH,CARD,MOBILE,CREDIT,OTHER',
        ]);

        try {
            // Business logic in service
            $transaction = $this->posService->processSale(
                $validated['items'],
                $validated
            );

            return response()->json([
                'success' => true,
                'transaction' => $transaction,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
```

### Naming Conventions

**Controllers:**
- Singular noun + "Controller"
- Example: `ProductController`, `TransactionController`

**Models:**
- Singular noun, PascalCase
- Example: `Product`, `ProductBatch`, `TransactionItem`

**Methods:**
- camelCase
- RESTful: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`
- Custom: descriptive verbs (e.g., `processSale`, `recordPayment`)

**Variables:**
- camelCase
- Descriptive names
- Example: `$transaction`, `$productBatch`, `$totalAmount`

**Database Tables:**
- Plural, snake_case
- Example: `products`, `product_batches`, `transaction_items`

**Routes:**
- Plural resources
- Example: `/products`, `/transactions`, `/purchase-orders`

### Code Organization

**Services:**
```php
namespace App\Services;

class PosService
{
    public function processSale(array $items, array $data): Transaction
    {
        // All business logic here
        // Return typed responses
    }
}
```

**Controllers:**
- Thin controllers
- Delegate to services
- Handle HTTP only

**Models:**
- Relationships
- Scopes
- Accessors/Mutators
- Business methods

---

## Database Design

### Entity Relationship Diagram

```
Users (1) ──────────── (*) Transactions
      │                      ├─ TransactionItems (1-*) Products
      │                      │                           └─ ProductBatches
      │                      └─ Returns (self-ref)
      │
      ├───────────────────── (*) PurchaseOrders
      │                      └─ PurchaseOrderItems
      │
      ├───────────────────── (*) Customers
      │                      └─ CustomerCreditTransactions
      │
      └───────────────────── (*) Dues
                             └─ DuePayments

Suppliers (1) ──── (*) Products
          │
          └──────────────── (*) PurchaseOrders
```

### Key Tables

**Core Tables:**
1. `users` - Authentication & authorization
2. `products` - Medicine catalog
3. `product_batches` - FIFO inventory tracking
4. `transactions` - Sales & returns
5. `transaction_items` - Line items

**Supply Chain:**
6. `suppliers` - Vendor management
7. `purchase_orders` - Stock orders
8. `purchase_order_items` - Order line items

**Credit Management:**
9. `customers` - Customer accounts
10. `customer_credit_transactions` - Audit trail
11. `dues` - Simple due tracking
12. `due_payments` - Payment records

### Indexing Strategy

**Performance Indexes:**
```sql
-- Products
INDEX idx_products_sku (sku)
INDEX idx_products_barcode (barcode)
INDEX idx_products_generic (generic_name)
INDEX idx_products_brand (brand_name)
INDEX idx_products_active (is_active)

-- Product Batches
INDEX idx_batches_product (product_id)
INDEX idx_batches_expiry (expiry_date)
INDEX idx_batches_active (is_active)

-- Transactions
INDEX idx_transactions_user (user_id)
INDEX idx_transactions_customer (customer_id)
INDEX idx_transactions_type (type)
INDEX idx_transactions_date (created_at)

-- Customers
UNIQUE idx_customers_phone (phone)
INDEX idx_customers_credit (credit_enabled)
```

### Migration Best Practices

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->decimal('selling_price', 10, 2);
            $table->integer('current_stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('sku');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

---

## Service Layer

### Purpose

Services encapsulate complex business logic separate from controllers:

- **Transaction Safety** - Database transactions
- **Reusability** - Used by multiple controllers
- **Testability** - Easy to unit test
- **Maintainability** - Single responsibility

### Example: PosService

```php
<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class PosService
{
    /**
     * Process a sale transaction
     *
     * @param array $items Cart items
     * @param array $data Transaction data
     * @return Transaction
     * @throws \Exception
     */
    public function processSale(array $items, array $data): Transaction
    {
        return DB::transaction(function () use ($items, $data) {
            // 1. Validate stock availability
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->current_stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }
            }

            // 2. Create transaction
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'customer_id' => $data['customer_id'] ?? null,
                'type' => 'SALE',
                'subtotal' => $this->calculateSubtotal($items),
                'discount' => $data['discount'] ?? 0,
                'total' => $this->calculateTotal($items, $data),
                'payment_method' => $data['payment_method'],
                'is_credit' => $data['payment_method'] === 'CREDIT',
            ]);

            // 3. Process items and deduct stock
            foreach ($items as $item) {
                $this->processItem($transaction, $item);
            }

            // 4. Handle credit sale
            if ($transaction->is_credit) {
                $this->handleCreditSale($transaction);
            }

            return $transaction->fresh('items.product', 'items.batch');
        });
    }

    protected function processItem(Transaction $transaction, array $item): void
    {
        $product = Product::findOrFail($item['product_id']);
        $batch = $product->batches()
            ->where('quantity_remaining', '>', 0)
            ->orderBy('expiry_date', 'ASC')
            ->firstOrFail();

        // Create transaction item
        $transaction->items()->create([
            'product_id' => $product->id,
            'batch_id' => $batch->id,
            'quantity' => $item['quantity'],
            'unit_price' => $product->selling_price,
            'subtotal' => $item['quantity'] * $product->selling_price,
        ]);

        // Deduct stock
        $batch->decrement('quantity_remaining', $item['quantity']);
        $product->decrement('current_stock', $item['quantity']);
    }

    // ... more methods
}
```

### Service Injection

In controllers:

```php
class TransactionController extends Controller
{
    public function __construct(
        protected PosService $posService
    ) {}

    public function store(Request $request)
    {
        $transaction = $this->posService->processSale(
            $request->input('items'),
            $request->all()
        );

        return response()->json(['transaction' => $transaction]);
    }
}
```

---

## Frontend Architecture

### Alpine.js Components

**POS Cart Management:**

```html
<div x-data="posApp()" x-init="init()">
    <!-- Product Search -->
    <input
        type="text"
        x-model="searchTerm"
        @input.debounce.300ms="searchProducts()"
        placeholder="Search products...">

    <!-- Search Results -->
    <div x-show="searchResults.length > 0">
        <template x-for="product in searchResults" :key="product.id">
            <div @click="addToCart(product)">
                <span x-text="product.name"></span>
                <span x-text="'৳' + product.selling_price"></span>
            </div>
        </template>
    </div>

    <!-- Cart -->
    <div>
        <template x-for="(item, index) in cart" :key="index">
            <div>
                <span x-text="item.name"></span>
                <input type="number" x-model.number="item.quantity" @input="calculateTotals()">
                <span x-text="'৳' + (item.quantity * item.price).toFixed(2)"></span>
                <button @click="removeFromCart(index)">Remove</button>
            </div>
        </template>
    </div>

    <!-- Totals -->
    <div>
        <div>Subtotal: ৳<span x-text="subtotal.toFixed(2)"></span></div>
        <div>Discount: ৳<input type="number" x-model.number="discount" @input="calculateTotals()"></div>
        <div>Total: ৳<span x-text="total.toFixed(2)"></span></div>
    </div>

    <!-- Complete Sale -->
    <button @click="completeSale()" :disabled="cart.length === 0">
        Complete Sale
    </button>
</div>

<script>
function posApp() {
    return {
        searchTerm: '',
        searchResults: [],
        cart: [],
        subtotal: 0,
        discount: 0,
        total: 0,

        async searchProducts() {
            if (this.searchTerm.length < 2) {
                this.searchResults = [];
                return;
            }

            const response = await fetch(`/api/products/search?q=${this.searchTerm}`);
            const data = await response.json();
            this.searchResults = data.products;
        },

        addToCart(product) {
            const existing = this.cart.find(item => item.id === product.id);

            if (existing) {
                existing.quantity++;
            } else {
                this.cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.selling_price,
                    quantity: 1
                });
            }

            this.searchTerm = '';
            this.searchResults = [];
            this.calculateTotals();
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
            this.calculateTotals();
        },

        calculateTotals() {
            this.subtotal = this.cart.reduce((sum, item) => {
                return sum + (item.quantity * item.price);
            }, 0);

            this.total = this.subtotal - this.discount;
        },

        async completeSale() {
            const response = await fetch('/api/transactions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    items: this.cart.map(item => ({
                        product_id: item.id,
                        quantity: item.quantity,
                        unit_price: item.price
                    })),
                    discount: this.discount,
                    payment_method: 'CASH'
                })
            });

            const data = await response.json();

            if (data.success) {
                // Reset cart
                this.cart = [];
                this.discount = 0;
                this.calculateTotals();

                // Show success message
                alert('Sale completed!');
            }
        }
    }
}
</script>
```

### Tailwind CSS

**Configuration:**

```javascript
// tailwind.config.js
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#eff6ff',
                    // ... color scale
                    900: '#1e3a8a',
                }
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}
```

**Usage:**

```html
<div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Product Name</h2>
    <p class="text-sm text-gray-600">Description...</p>
    <div class="mt-4 flex justify-between items-center">
        <span class="text-lg font-semibold text-green-600">৳500</span>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            Add to Cart
        </button>
    </div>
</div>
```

---

## Testing

### Unit Testing

```php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\PosService;
use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PosServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PosService $posService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->posService = app(PosService::class);
    }

    public function test_process_sale_creates_transaction()
    {
        // Arrange
        $product = Product::factory()->create([
            'selling_price' => 100,
            'current_stock' => 10,
        ]);

        ProductBatch::factory()->create([
            'product_id' => $product->id,
            'quantity_remaining' => 10,
            'expiry_date' => now()->addYear(),
        ]);

        $items = [
            ['product_id' => $product->id, 'quantity' => 2]
        ];

        $data = [
            'payment_method' => 'CASH',
            'discount' => 0,
        ];

        // Act
        $transaction = $this->posService->processSale($items, $data);

        // Assert
        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'type' => 'SALE',
            'total' => 200,
        ]);

        $this->assertEquals(8, $product->fresh()->current_stock);
    }
}
```

### Feature Testing

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PosTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_access_pos()
    {
        $user = User::factory()->create(['role' => 'cashier']);

        $response = $this->actingAs($user)->get('/pos');

        $response->assertStatus(200);
    }

    public function test_can_complete_sale()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['current_stock' => 10]);

        $response = $this->actingAs($user)->postJson('/api/transactions', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ],
            'payment_method' => 'CASH',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }
}
```

### Running Tests

```bash
# Run all tests
docker-compose exec app php artisan test

# Run specific test
docker-compose exec app php artisan test --filter PosServiceTest

# Run with coverage
docker-compose exec app php artisan test --coverage

# Run feature tests only
docker-compose exec app php artisan test --testsuite=Feature
```

---

## Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed production deployment guide.

### Quick Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure database credentials
- [ ] Run `composer install --no-dev --optimize-autoloader`
- [ ] Run `npm run build`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Set up SSL certificate
- [ ] Configure backups
- [ ] Set up monitoring

---

## Contributing

### Git Workflow

```bash
# 1. Create feature branch
git checkout -b feature/new-feature

# 2. Make changes and commit
git add .
git commit -m "Add new feature"

# 3. Push to remote
git push origin feature/new-feature

# 4. Create pull request
# ... via GitHub interface

# 5. After merge, update main
git checkout main
git pull origin main
```

### Commit Message Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation
- `style`: Code style (formatting)
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Maintenance

**Example:**
```
feat(pos): Add discount support to POS

- Added discount input field
- Updated total calculation
- Added validation for discount amount

Closes #123
```

### Code Review Checklist

- [ ] Code follows PSR-12 standards
- [ ] Business logic in services, not controllers
- [ ] Database queries optimized (N+1 checked)
- [ ] Validation rules present
- [ ] Error handling implemented
- [ ] Tests added/updated
- [ ] Documentation updated
- [ ] No sensitive data committed

---

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Alpine.js Documentation](https://alpinejs.dev)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Chart.js Documentation](https://www.chartjs.org/docs)
- [API Reference](API_REFERENCE.md)
- [Database Schema](DATABASE_SCHEMA.md)

---

**Document Version:** 1.0
**System Version:** 2.6.0
**Last Updated:** November 2025

---

*End of Developer Guide*

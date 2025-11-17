# BLORIEN Pharma - Technical Implementation Plan

## Simplified Task-Based Approach with Docker

**Version:** 2.0
**Status:** Ready for Development
**Environment:** Docker-based (Platform Independent)

---

## Project Overview

**Name:** BLORIEN Pharma - Pharmacy Management Software

**Objective:** Build a pharmacy management system with POS, inventory, staff management, and sales tracking.

**Scope (MVP):**

- ✅ User authentication
- ✅ Product inventory management
- ✅ Batch and expiry tracking
- ✅ Point of Sale interface
- ✅ Sales transactions
- ✅ Receipt generation
- ✅ Daily dashboard

**Not Included (Phase 2+):**

- ❌ Advanced reporting
- ❌ Supplier management
- ❌ Customer credit
- ❌ Multi-location support
- ❌ Analytics

---

## Architecture Overview

```bash
┌─────────────────────────────────────────────────────────┐
│                    DOCKER ENVIRONMENT                   │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ┌──────────────────────────────────────────────────┐  │
│  │           Web Container (Nginx)                  │  │
│  │  - Port: 8000                                   │  │
│  │  - Serves Laravel app                           │  │
│  └──────────────────────────────────────────────────┘  │
│                          ↓                              │
│  ┌──────────────────────────────────────────────────┐  │
│  │      PHP-FPM Container (Laravel App)             │  │
│  │  - Laravel 12                                    │  │
│  │  - PHP 8.4                                       │  │
│  │  - Application code                             │  │
│  └──────────────────────────────────────────────────┘  │
│                          ↓                              │
│  ┌──────────────────────────────────────────────────┐  │
│  │        MySQL Container (Database)                │  │
│  │  - Port: 3306                                    │  │
│  │  - 5 normalized tables                          │  │
│  └──────────────────────────────────────────────────┘  │
│                                                         │
│  Volumes (Persist Data):                              │
│  - /app (Laravel code)                               │
│  - /var/lib/mysql (Database)                         │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

## Technology Stack

| Component         | Technology              | Version           |
| ----------------- | ----------------------- | ----------------- |
| Container Runtime | Docker                  | Latest            |
| Web Server        | Nginx                   | Compatible        |
| PHP Runtime       | PHP-FPM                 | Compatible        |
| Framework         | Laravel                 | Latest            |
| Database          | MySQL                   | Latest Compatible |
| Frontend          | TailwindCSS + Alpine.js | Latest            |
| Package Manager   | Composer                | Latest            |

---

## Docker Setup

### File Structure

```bash
blorien-pharma/
├── docker-compose.yml (Container orchestration)
├── Dockerfile (PHP-FPM image)
├── docker/
│   ├── nginx/
│   │   ├── Dockerfile
│   │   └── nginx.conf
│   ├── php/
│   │   └── Dockerfile
│   └── mysql/
│       └── my.cnf
├── app/ (Laravel application)
├── .env.example
└── README.md
```

### docker-compose.yml

Defines 3 services:

1. **Nginx** - Web server (port 8000)
2. **PHP-FPM** - Application server
3. **MySQL** - Database (port 3306)

All services communicate through internal Docker network.
Volumes persist data between restarts.

### Getting Started

**Start all containers:**

```bash
docker-compose up -d
```

**Access application:**

```bash
http://localhost:8000
```

**SSH into PHP container:**

```bash
docker-compose exec app bash
```

---

## Development Timeline

### Week 1: Docker Setup + Core System

#### Task 1.1: Docker Infrastructure Setup

- Create docker-compose.yml with Nginx, PHP-FPM, MySQL
- Configure volume mounts for code and database
- Set up networking between containers
- Create environment files

#### Task 1.2: Laravel Project Initialization

- Initialize Laravel 12 project in PHP container
- Configure Laravel environment
- Test Laravel app loads at localhost:8000

#### Task 1.3: Database Setup

- Create 5 database migrations
- Define table structures (users, products, batches, transactions, items)
- Test migrations run successfully

#### Task 1.4: Authentication System

- Create authentication controller
- Build login page
- Implement login/logout functionality
- Test login flow

**Deliverable:** Docker environment running, login working

---

### Week 2: User & Product Management

#### Task 2.1: User Management Module

- Create User model and controller
- Build user creation functionality
- Implement role-based access (Owner, Cashier, Manager)
- Create user list view

#### Task 2.2: Product Management Module

- Create Product model and controller
- Build product CRUD (Create, Read, Update, Delete)
- Add product search functionality
- Create product list and form views

#### Task 2.3: Batch Management Module

- Create ProductBatch model
- Implement batch creation for products
- Add expiry date tracking
- Build batch list view

#### Task 2.4: Inventory Service

- Create service class for inventory operations
- Implement stock quantity management
- Build low-stock alert logic

**Deliverable:** Can create products and batches, view inventory

---

### POS & Transactions

#### Task 3.1: POS Interface Setup

- Create POS controller
- Build search interface (Alpine.js)
- Implement shopping cart functionality
- Design bill summary layout

#### Task 3.2: Transaction Processing

- Create Transaction and TransactionItem models
- Implement sale completion logic
- Update inventory automatically after sale
- Build transaction history view

#### Task 3.3: Receipt Generation

- Create receipt service
- Implement receipt template
- Add thermal printer support
- Build receipt view

#### Task 3.4: Return Processing

- Implement return/undo functionality
- Reverse transaction logic
- Restore inventory on return
- Update transaction history

**Deliverable:** Full POS workflow working end-to-end

---

### Dashboard + Polish

#### Task 5.1: Dashboard Module

- Create dashboard controller
- Build today's statistics view
- Implement sales metrics calculation
- Display top-selling products

#### Task 5.2: Alert System

- Create alerts for low stock
- Implement expiry date alerts
- Build alert notification UI
- Display alerts on dashboard

#### Task 5.3: Testing & Bug Fixes

- Test all features
- Fix bugs
- Performance optimization
- Security review

#### Task 5.4: Documentation & Deployment

- Create user guide
- Write installation instructions
- Build deployment script
- Package for distribution

**Deliverable:** Production-ready MVP

---

## Database Design

### 5 Core Tables

#### Table 1: Users

- Stores staff accounts
- Fields: id, name, email, password, role, is_active
- Relationships: One user → Many transactions

#### Table 2: Products

- Stores pharmacy products
- Fields: id, name, sku, purchase_price, selling_price, current_stock, min_stock
- Relationships: One product → Many batches, Many transaction items

#### Table 3: ProductBatches

- Tracks product batches with expiry dates
- Fields: id, product_id, batch_number, expiry_date, quantity_remaining
- Relationships: One batch → Many transaction items

#### Table 4: Transactions

- Records all sales and returns
- Fields: id, type (SALE/RETURN), total, payment_method, user_id, created_at
- Relationships: One transaction → Many transaction items

#### Table 5: TransactionItems

- Individual items in each transaction
- Fields: id, transaction_id, product_id, batch_id, quantity, unit_price
- Relationships: Many items per transaction

---

## API Endpoints

### Authentication

- **POST /login** - User login
- **POST /logout** - User logout
- **POST /setup** - Initial system setup

### Products

- **GET /api/products** - List all products
- **POST /api/products** - Create product
- **GET /api/products/search?q=query** - Search products
- **POST /api/products/{id}** - Update product
- **DELETE /api/products/{id}** - Delete product

### Batches

- **POST /api/products/{id}/batches** - Add batch
- **GET /api/batches/expiring** - Get expiring soon (30 days)
- **GET /api/batches/expired** - Get already expired

### Transactions

- **POST /api/transactions** - Complete sale
- **GET /api/transactions/today** - Get today's transactions
- **GET /api/transactions/recent** - Get recent transactions
- **GET /api/transactions/{id}** - Get transaction details
- **POST /api/transactions/{id}/return** - Process return

### Users

- **GET /api/users** - List users
- **POST /api/users** - Create user

### Dashboard

- **GET /api/dashboard/stats** - Get dashboard statistics

---

## Project Structure

```bash
blorien-pharma/
├── docker-compose.yml
├── Dockerfile
├── docker/
│   ├── nginx/
│   ├── php/
│   └── mysql/
│
└── app/ (Laravel Application)
    ├── app/
    │   ├── Http/Controllers/
    │   │   ├── AuthController
    │   │   ├── ProductController
    │   │   ├── BatchController
    │   │   ├── PosController
    │   │   ├── TransactionController
    │   │   ├── UserController
    │   │   ├── DashboardController
    │   │   └── ReceiptController
    │   │
    │   ├── Models/
    │   │   ├── User
    │   │   ├── Product
    │   │   ├── ProductBatch
    │   │   ├── Transaction
    │   │   └── TransactionItem
    │   │
    │   └── Services/
    │       ├── PosService
    │       ├── InventoryService
    │       ├── ReceiptService
    │       └── DashboardService
    │
    ├── database/
    │   ├── migrations/
    │   └── seeders/
    │
    ├── resources/views/
    │   ├── layouts/
    │   ├── auth/
    │   ├── dashboard/
    │   ├── products/
    │   ├── pos/
    │   ├── transactions/
    │   ├── users/
    │   └── settings/
    │
    ├── routes/
    │   ├── web.php
    │   └── api.php
    │
    └── public/
        └── css/, js/
```

---

## Docker Development Workflow

### Initial Setup

#### Step 1: Clone/Create Project

```bash
Get blorien-pharma files
```

#### Step 2: Start Docker Environment

```bash
Navigate to project directory
Run: docker-compose up -d
```

#### Step 3: Initialize Laravel

```bash
SSH into PHP container
Install dependencies and run migrations
```

#### Step 4: Access Application

```bash
Open browser to http://localhost:8000
```

### Daily Development

#### Access Container

```bash
docker-compose exec app bash
```

#### Run Commands

```bash
php artisan migrate (run migrations)
php artisan tinker (database console)
php artisan serve (start dev server)
```

#### View Logs

```bash
docker-compose logs -f app (Laravel logs)
docker-compose logs -f db (Database logs)
docker-compose logs -f web (Nginx logs)
```

#### Stop Environment

```bash
docker-compose down
```

---

## Modules Breakdown

### Module 1: Core System

**Components:** Docker setup, Laravel initialization, database setup
**Tasks:** Environment configuration, service setup, database migrations

### Module 2: Authentication

**Components:** Login/logout, user sessions, role-based access
**Tasks:** Auth controller, login view, permission checks

### Module 3: Inventory Management

**Components:** Product CRUD, batch tracking, expiry management
**Tasks:** Product controller, batch operations, search functionality

### Module 4: Point of Sale

**Components:** POS interface, shopping cart, transaction processing
**Tasks:** POS controller, checkout logic, receipt generation

### Module 5: Transactions

**Components:** Sale recording, returns, transaction history
**Tasks:** Transaction controller, inventory updates, return logic

### Module 6: Dashboard

**Components:** Statistics, alerts, metrics
**Tasks:** Dashboard controller, stat calculations, alert logic

### Module 7: User Management

**Components:** User creation, role assignment, access control
**Tasks:** User controller, permission management

### Module 8: Reporting (Basic)

**Components:** Daily reports, transaction history
**Tasks:** Report views, data aggregation

---

## Key Development Tasks

### Task Priority 1 (Critical Path)

- **Docker Setup** - All development depends on this
- **Database Schema** - Foundation for all operations
- **Authentication** - Gatekeeper for system
- **POS Module** - Core revenue-generating feature
- **Transaction Recording** - Data persistence

### Task Priority 2 (Important)

- **Inventory Management** - Stock tracking
- **Receipt Generation** - Customer facing
- **Dashboard** - Owner visibility
- **User Management** - Staff access

### Task Priority 3 (Nice to Have)

- **Advanced Alerts** - Notifications
- **Reports** - Data analysis
- **Performance Optimization** - Speed

---

## Quality Gates

### Docker Environment

- ✅ All 3 containers run without errors
- ✅ Containers can communicate
- ✅ Volumes persist data
- ✅ Application accessible at localhost:8000

### Database

- ✅ All 5 tables created
- ✅ Migrations run successfully
- ✅ Foreign key constraints work
- ✅ Sample data seeds correctly

### Core Features

- ✅ Login/logout works
- ✅ Products can be added/edited
- ✅ Sale transaction completes
- ✅ Inventory updates automatically
- ✅ Receipt prints

### System Stability

- ✅ No database errors
- ✅ No PHP errors
- ✅ Response time < 2 seconds
- ✅ All data persists

---

## Development Best Practices

### Version Control

- Use Git for all code changes
- Commit daily with clear messages
- Create branches for major features

### Testing

- Manual test each feature before moving to next
- Test edge cases (empty cart, low stock, etc.)
- Test on different browsers

### Code Organization

- Keep controllers thin (use services for logic)
- Use meaningful variable and function names
- Comment complex logic sections

### Database migration and backup

- Use migrations for all schema changes
- Never modify production data manually
- Regular backups

### Docker

- Never modify containers directly (use volumes)
- Always use docker-compose for consistency
- Document any custom configuration

---

## Deployment Approach

### Development Environment

#### Running on developer's machine via Docker

### Testing Environment

#### Same Docker setup, separate database

### Production Environment

#### Docker on server/cloud with persistent storage

**All environments use same codebase and Docker configuration.**

---

## Success Metrics

**By End of Week 1:**

- Docker environment working
- Login functional
- Dashboard showing basic stats

**By End of Week 2:**

- Products manageable
- Batches trackable
- Inventory visible

**By End of Week 4:**

- Full POS working
- Sales recording correctly
- Receipts printing

**By End of Week 5:**

- All features tested
- No critical bugs
- Documentation complete
- Ready for production

---

## Risk Mitigation

| Risk               | Mitigation                                      |
| ------------------ | ----------------------------------------------- |
| Docker issues      | Pre-test on multiple machines before starting   |
| Database locks     | Implement transaction handling properly         |
| Performance issues | Monitor database queries, add indexes as needed |
| Data loss          | Regular automated backups                       |
| Security issues    | Validate all inputs, hash passwords             |

---

## Support & Debugging

### Common Docker Issues

- Check docker-compose.yml syntax
- Verify port availability (8000, 3306)
- Clear volumes if data corrupted: `docker-compose down -v`

### Laravel Issues

- Check .env configuration
- Review migration errors
- Check Laravel logs in container

### Database Issues

- Verify MySQL is running: `docker-compose ps`
- Check connection string in .env
- Review database logs

---

## Documentation Files

### README.md

Quick start guide with Docker commands

### INSTALLATION.md

Detailed setup instructions for team

### USER_GUIDE.md

Step-by-step guide for pharmacy staff

### DEVELOPER_GUIDE.md

Development environment setup and workflow

### API_DOCUMENTATION.md

Complete API endpoint reference

---

## Final Deliverables

**Week 5 Output:**

- ✅ Complete working Laravel application
- ✅ Docker environment configuration
- ✅ MySQL database with all tables
- ✅ All 8 modules implemented
- ✅ Full POS workflow working
- ✅ Receipt generation working
- ✅ User management working
- ✅ Dashboard functional
- ✅ Complete documentation
- ✅ Ready to deploy

---

## Summary

**This plan provides:**

- ✅ Docker-based development (works on any machine)
- ✅ 8 core modules to build
- ✅ Clear weekly milestones
- ✅ Task breakdown with explanations
- ✅ Architecture overview
- ✅ No code bloat - just specifications
- ✅ Focus on execution, not theory

### Production-Ready BLORIEN Pharma

---

# BLORIEN Pharma - Pharmacy Management System

A comprehensive yet simple pharmacy management system built for **Bangladesh small pharmacies**, featuring Point of Sale (POS), inventory management, batch tracking, notebook-style due tracking, and advanced analytics.

**Version**: 2.5.0
**Status**: Production Ready for Small Pharmacies

---

## 🎯 Target Market

- Walk-in customer focused
- Simple workflows with accurate record-keeping
- Notebook-style due tracking
- Generic/brand name medicine search
- Optional advanced features for growth

---

## ✨ Key Features

### Core System

- **User Authentication**: Role-based access (Owner, Manager, Cashier)
- **Product Management**: Full CRUD with generic/brand name support
- **Batch Management**: FIFO tracking with expiry monitoring
- **Point of Sale**: Interactive POS with cart management
- **Transaction Management**: Sales, returns, and transaction history
- **Receipt Generation**: Printable receipts with thermal printer support
- **Inventory Alerts**: Low stock and expiry warnings
- **Dashboard**: Real-time statistics and metrics
- **Quick Phone Lookup**: Type phone → customer auto-fills
- **Generic/Brand Search**: Find "Napa" or "Paracetamol" - both work!
- **Notebook-Style Dues (বাকি)**: Simple due tracking like digital notebook
- **Flexible Workflows**: Optional customer profiles, skip steps as needed
- **Partial Payments**: Track and collect payments over ti

### Supply Chain Management

- **Supplier Management**: Track suppliers with contact information
- **Purchase Orders**: Create, manage, and receive stock orders
- **Automatic Stock Updates**: Inventory updates upon order receipt
- **Batch Creation**: Automatic batch generation with expiry tracking
- **Supplier Performance**: Track orders and spending

### Customer Management

- **Customer Accounts**: Store customer information with credit tracking
- **Credit System**: Set credit limits and track balances
- **Credit Sales**: Process sales on credit through POS
- **Payment Recording**: Track customer payments
- **Balance Adjustments**: Manual adjustments with audit trail

### Reporting & Analytics

- **6 Comprehensive Reports**:
  - Sales Reports
  - Profit Analysis
  - Inventory Reports
  - Top Products
  - Supplier Performance
  - Customer Credit Reports
- **Interactive Analytics Dashboard**: Visual insights with Chart.js
  - Sales trends (30-day charts)
  - Payment method distribution
  - Inventory status visualization
  - Top products charts
  - Credit utilization graphs

---

## 🚀 Technology Stack

- **Backend**: Laravel 12, PHP 8.4
- **Database**: MySQL 8.0 (12 tables, see [DATABASE.md](DATABASE.md))
- **Frontend**: TailwindCSS, Alpine.js
- **Charts**: Chart.js 4.4.0
- **Infrastructure**: Docker (Nginx + PHP-FPM + MySQL)

---

## 📦 Quick Start

### Prerequisites

- Docker and Docker Compose installed
- Git

### Installation

1. **Clone the repository**

```bash
git clone git@github.com:blorien-tech/pharma.git
cd pharma
```

2. **Start Docker containers**

```bash
docker compose up -d
```

3. **Access the PHP container**

```bash
docker compose exec app bash
```

4. **Install dependencies and setup**

```bash
composer install
cp .env.example app/.env
php artisan key:generate
php artisan migrate
```

5. **Access the application**

```
http://localhost:8000
```

6. **Initial Setup**

- Navigate to `http://localhost:8000/setup`
- Create your owner account
- Start using the system!

---

## 📚 Documentation

- **[DATABASE.md](DATABASE.md)** - Complete database schema (12 tables)
- **[ARCHITECTURE.md](ARCHITECTURE.md)** - System architecture and design patterns
- **[USER_GUIDE.md](USER_GUIDE.md)** - How to use the system (for pharmacy staff)
- **[INSTALLATION.md](INSTALLATION.md)** - Detailed setup instructions
- **[ROADMAP.md](ROADMAP.md)** - Future development plans

---

## 🔧 Development

### Run Migrations

```bash
docker compose exec app php artisan migrate
```

### Clear Cache

```bash
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
```

### View Logs

```bash
docker compose logs -f app
```

### Database Backup

```bash
docker compose exec db mysqldump -u root -p blorien_pharma > backup.sql
```

---

## 🎯 System Philosophy

### FLEXIBILITY with ACCURACY

✅ **FLEXIBILITY**

- Optional customer profiles
- Skip supplier onboarding if needed
- Quick workflows (30-second sales)
- Support "rule-breaking" behavior

✅ **ACCURACY**

- Complete audit trails
- Payment history tracking
- Automatic status updates
- Professional-grade reporting

✅ **SIMPLICITY**

- Like digital notebook
- Minimal required fields
- No training needed for basics

---

## 📊 System Statistics

- **Total Tables**: 12
- **Total Controllers**: 13
- **Total Models**: 12
- **Web Routes**: 100+
- **API Routes**: 25+
- **Views**: 50+
- **Migrations**: 10

---

## 🎓 User Roles & Permissions

### Owner

- Full system access
- Manage users
- All reports
- System settings

### Manager

- Product & inventory management
- View reports
- Manage users
- Purchase orders and suppliers

### Cashier

- Process sales (POS)
- View products
- Mark sales as due
- View own transactions

---

## 📞 Support

For issues or questions:

1. Check [USER_GUIDE.md](USER_GUIDE.md)
2. Review [DATABASE.md](DATABASE.md) for schema questions
3. See [ROADMAP.md](ROADMAP.md) for planned features
4. Create an issue in the repository

---

## 📝 License

MIT License

---

## 👥 Credits

Developed by **BLORIEN**

Built specifically for Bangladesh small pharmacies with:

- Understanding of local workflows
- Bengali language support
- Generic/brand medicine search
- Simple notebook-style tracking
- Professional accuracy

---

**Current Version**: 2.5 (Phase 3A Complete - January 2025)
**Status**: ✅ Production Ready

_Built for Bangladesh, built for simplicity, built for accuracy._ 🇧🇩

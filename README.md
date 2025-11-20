# BLORIEN Pharma - Pharmacy Management System

A comprehensive yet simple pharmacy management system built for **Bangladesh small pharmacies**, featuring Point of Sale (POS), inventory management, batch tracking, notebook-style due tracking (বাকি হিসাব), multi-language support, and advanced analytics.

**Version**: 2.6 (Phase 3B Complete)
**Status**: ✅ Production Ready for Small Pharmacies
**Languages**: English, Bengali (বাংলা)

---

## 🎯 Target Market

**Small town pharmacies in Bangladesh (1-2 staff)**
- Walk-in customer focused
- Simple workflows with accurate record-keeping
- Notebook-style due tracking
- Generic/brand name medicine search
- Optional advanced features for growth

---

## ✨ Key Features

### Core System
- ✅ **User Authentication**: Role-based access (Owner, Manager, Cashier)
- ✅ **Product Management**: Full CRUD with generic/brand name support
- ✅ **Batch Management**: FIFO tracking with expiry monitoring
- ✅ **Point of Sale**: Interactive POS with cart management
- ✅ **Transaction Management**: Sales, returns, and transaction history
- ✅ **Receipt Generation**: Printable receipts with thermal printer support
- ✅ **Inventory Alerts**: Low stock and expiry warnings
- ✅ **Dashboard**: Real-time statistics and metrics

### Phase 3A - Small Pharmacy Features
- ✅ **Quick Phone Lookup**: Type phone → customer auto-fills
- ✅ **Generic/Brand Search**: Find "Napa" or "Paracetamol" - both work!
- ✅ **Notebook-Style Dues (বাকি)**: Simple due tracking like digital notebook
- ✅ **Flexible Workflows**: Optional customer profiles, skip steps as needed
- ✅ **Partial Payments**: Track and collect payments over time

### Phase 3B - Workflow Enhancements 🆕
- ✅ **Quick Stock Add**: Add stock in < 20 seconds without full purchase order
- ✅ **Daily Closing Summary**: One-click end-of-day report with print support
- ✅ **Dashboard Dues Widgets**: Instant visibility of pending and overdue dues
- ✅ **Simplified Navigation**: Basic/Advanced mode toggle for cleaner interface
- ✅ **Performance Optimizations**: Cached queries, indexed tables, debounced search

### Multi-Language Support 🆕
- ✅ **English & Bengali**: Full system translation in both languages
- ✅ **Database-Stored Preferences**: Language choice persists across sessions
- ✅ **Instant Language Switching**: One-click toggle in navigation bar
- ✅ **Validation Messages**: Form errors in user's preferred language
- ✅ **JavaScript Translations**: Alerts and dynamic messages localized

### Supply Chain Management
- ✅ **Supplier Management**: Track suppliers with contact information
- ✅ **Purchase Orders**: Create, manage, and receive stock orders
- ✅ **Automatic Stock Updates**: Inventory updates upon order receipt
- ✅ **Batch Creation**: Automatic batch generation with expiry tracking
- ✅ **Supplier Performance**: Track orders and spending

### Customer Management
- ✅ **Customer Accounts**: Store customer information with credit tracking
- ✅ **Credit System**: Set credit limits and track balances
- ✅ **Credit Sales**: Process sales on credit through POS
- ✅ **Payment Recording**: Track customer payments
- ✅ **Balance Adjustments**: Manual adjustments with audit trail

### Reporting & Analytics
- ✅ **6 Comprehensive Reports**:
  - Sales Reports
  - Profit Analysis
  - Inventory Reports
  - Top Products
  - Supplier Performance
  - Customer Credit Reports
- ✅ **Interactive Analytics Dashboard**: Visual insights with Chart.js
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
- **Localization**: Laravel Translation System (English, Bengali)
- **Infrastructure**: Docker (Nginx + PHP-FPM + MySQL)

---

## 📦 Quick Start

### Prerequisites

- Docker and Docker Compose installed
- Git

### Installation

1. **Clone the repository**
```bash
git clone <repository-url>
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
- **[ARCHITECTURE_SUMMARY.md](ARCHITECTURE_SUMMARY.md)** - System architecture and design patterns
- **[USER_GUIDE.md](USER_GUIDE.md)** - How to use the system (for pharmacy staff)
- **[INSTALLATION.md](INSTALLATION.md)** - Detailed setup instructions
- **[ROADMAP.md](ROADMAP.md)** - Future development plans

---

## 💡 Phase 3A Highlights

### Real-World Example

**Before:**
```
Customer: "I need Napa, mark it as due"
Shopkeeper: Must create full customer profile first...
```

**After Phase 3A:**
```
1. Type "Napa" in POS → Found by brand name ✓
2. Check "Mark as Due (বাকি)" ✓
3. Enter "Rahman" → Done! ✓
   (phone optional, no profile needed)
4. Sale complete, due recorded ✓
```

### Key Improvements

- **Quick Customer Lookup**: Type phone number → instant match
- **Smart Product Search**: "Napa" or "Paracetamol" both work
- **Simple Due Tracking**: Like digital notebook, just name + amount
- **Partial Payments**: Collect ৳200 today, ৳300 later
- **Audit Trail**: Every payment tracked automatically

---

## 🗺️ Implementation Status

### ✅ Completed (Phases 1, 2, 3A, 3B)

| Phase | Features | Status |
|-------|----------|--------|
| **Phase 1 (MVP)** | Auth, Products, Batches, POS, Transactions | ✅ Complete |
| **Phase 2** | Suppliers, POs, Customers, Credit, Reports, Analytics | ✅ Complete |
| **Phase 3A** | Dues, Phone Lookup, Generic/Brand Search | ✅ Complete |
| **Phase 3B** | Quick Stock, Daily Closing, Multi-Language, Performance | ✅ Complete |

### 🔜 Next Priorities

| Phase | Features | Priority |
|-------|----------|----------|
| **Phase 4** | DGDA compliance (Schedule drugs, Prescriptions) | High |
| **Phase 5** | Mobile app (React Native) | Medium |
| **Phase 6** | Optional bKash/Nagad integration | Low |

See [ROADMAP.md](ROADMAP.md) for details.

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
- Bengali labels (বাকি) for familiarity
- Minimal required fields
- No training needed for basics

---

## 📊 System Statistics

- **Total Tables**: 12
- **Total Controllers**: 14
- **Total Models**: 12
- **Total Middlewares**: 3
- **Web Routes**: 105+
- **API Routes**: 27+
- **Views**: 52+
- **Migrations**: 12
- **Translation Keys**: 200+
- **Languages**: 2 (English, Bengali)

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

## 💰 Pricing Strategy

**Target**: ৳1,500-3,000/month
- Affordable for small pharmacies
- No upfront cost (SaaS model)
- Includes updates and support

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

Developed by **BLORIEN Tech**

Built specifically for Bangladesh small pharmacies with:
- Full Bengali language support
- Understanding of local workflows
- Generic/brand medicine search
- Simple notebook-style tracking (বাকি হিসাব)
- Professional accuracy with flexibility

---

**Current Version**: 2.6 (Phase 3B Complete - January 2025)
**Status**: ✅ Production Ready
**Latest**: Multi-language support, workflow enhancements, performance optimizations

*Built for Bangladesh, built for simplicity, built for accuracy.* 🇧🇩

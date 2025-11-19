# BLORIEN Pharma - Pharmacy Management System

A comprehensive pharmacy management system built with Laravel 12, featuring Point of Sale (POS), inventory management, batch tracking, expiry monitoring, supplier management, customer credit system, and advanced analytics.

## Features

### Core System
- **User Authentication**: Role-based access control (Owner, Manager, Cashier)
- **Product Management**: Complete CRUD operations with SKU tracking
- **Batch Management**: Track product batches with expiry dates (FIFO)
- **Point of Sale**: Interactive POS interface with real-time cart management
- **Transaction Management**: Sales recording, returns, and transaction history
- **Receipt Generation**: Printable receipts with thermal printer support
- **Inventory Alerts**: Low stock and expiry date warnings
- **Dashboard**: Real-time statistics and metrics

### Supply Chain Management
- **Supplier Management**: Track suppliers with contact information
- **Purchase Orders**: Create, manage, and receive stock orders
- **Automatic Stock Updates**: Inventory updates upon order receipt
- **Batch Creation**: Automatic batch generation with expiry tracking
- **Supplier Performance**: Track orders and spending by supplier

### Customer Management
- **Customer Accounts**: Store customer information with credit tracking
- **Credit System**: Set credit limits and track balances
- **Credit Sales**: Process sales on credit through POS
- **Payment Recording**: Track customer payments with multiple methods
- **Balance Adjustments**: Manual adjustments with audit trail
- **Credit History**: Complete transaction history for each customer

### Reporting & Analytics
- **Sales Reports**: Date-based analysis with payment method breakdown
- **Profit Analysis**: Revenue vs. cost with margin calculations
- **Inventory Reports**: Stock valuation and potential profit
- **Top Products**: Best sellers by quantity and revenue
- **Supplier Reports**: Performance tracking and spending analysis
- **Customer Credit Reports**: Balance monitoring and utilization
- **Interactive Analytics Dashboard**: Visual insights with Chart.js
  - Sales trends (30-day charts)
  - Payment method distribution
  - Inventory status visualization
  - Top products charts
  - Credit utilization graphs

## Technology Stack

- **Backend**: Laravel 12, PHP 8.4
- **Database**: MySQL 8.0
- **Frontend**: TailwindCSS, Alpine.js
- **Infrastructure**: Docker (Nginx + PHP-FPM + MySQL)

## Quick Start

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
docker-compose up -d
```

3. **Access the PHP container**
```bash
docker-compose exec app bash
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

## Default Ports

- **Web Application**: http://localhost:8000
- **MySQL**: localhost:3306

## Project Structure

```
blorien-pharma/
├── docker/                 # Docker configuration
│   ├── nginx/             # Web server
│   ├── php/               # PHP-FPM
│   └── mysql/             # Database
├── app/                   # Laravel application
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   └── Services/
│   ├── database/migrations/
│   ├── resources/views/
│   └── routes/
├── docker-compose.yml     # Container orchestration
└── README.md
```

## Documentation

- [Installation Guide](INSTALLATION.md) - Detailed setup instructions
- [User Guide](USER_GUIDE.md) - How to use the system

## Core Modules

1. **Authentication** - Login, logout, and initial system setup
2. **User Management** - Create and manage staff accounts (Owner/Manager only)
3. **Product Management** - Products, batches, and inventory tracking
4. **Point of Sale** - Process sales transactions (cash and credit)
5. **Transactions** - View and manage sales/returns with filtering
6. **Dashboard** - Real-time statistics and system overview
7. **Alerts** - Low stock and expiry monitoring
8. **Suppliers** - Manage supplier relationships (Owner/Manager only)
9. **Purchase Orders** - Stock ordering and receiving (Owner/Manager only)
10. **Customers** - Customer accounts with credit management
11. **Reports** - Comprehensive business reports (6 types)
12. **Analytics** - Interactive dashboard with charts and visualizations

## Development

### Run Migrations
```bash
docker-compose exec app php artisan migrate
```

### Clear Cache
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

### View Logs
```bash
docker-compose logs -f app
```

## Support

For issues, please create an issue in the repository.

## License

MIT License

## Credits

Developed by BLORIEN Tech

# BLORIEN Pharma - Pharmacy Management System

A comprehensive pharmacy management system built with Laravel 12, featuring Point of Sale (POS), inventory management, batch tracking, and expiry monitoring.

## Features

- **User Authentication**: Role-based access control (Owner, Manager, Cashier)
- **Product Management**: Complete CRUD operations with SKU tracking
- **Batch Management**: Track product batches with expiry dates (FIFO)
- **Point of Sale**: Interactive POS interface with real-time cart management
- **Transaction Management**: Sales recording, returns, and transaction history
- **Receipt Generation**: Printable receipts with thermal printer support
- **Inventory Alerts**: Low stock and expiry date warnings
- **Dashboard**: Real-time statistics and metrics
- **Reports**: Daily sales reports and transaction history

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
2. **User Management** - Create and manage staff accounts
3. **Inventory Management** - Products and batch tracking
4. **Point of Sale** - Process sales transactions
5. **Transactions** - View and manage sales/returns
6. **Dashboard** - Statistics and system overview
7. **Alerts** - Low stock and expiry monitoring

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

# BLORIEN Pharma - Installation Guide

**Version:** 2.6.0
**Last Updated:** November 2025
**Audience:** System Administrators, DevOps Engineers

---

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Pre-Installation Checklist](#pre-installation-checklist)
3. [Installation Methods](#installation-methods)
4. [Docker Installation (Recommended)](#docker-installation-recommended)
5. [Manual Installation](#manual-installation)
6. [Initial Configuration](#initial-configuration)
7. [Post-Installation](#post-installation)
8. [Troubleshooting](#troubleshooting)

---

## System Requirements

### Minimum Requirements

| Component | Requirement |
|-----------|-------------|
| **CPU** | 2 cores |
| **RAM** | 2GB |
| **Disk** | 10GB SSD |
| **OS** | Ubuntu 20.04+, Debian 11+, or similar |
| **Network** | 10 Mbps |

### Recommended Requirements

| Component | Requirement |
|-----------|-------------|
| **CPU** | 4 cores |
| **RAM** | 4GB |
| **Disk** | 20GB SSD |
| **OS** | Ubuntu 22.04 LTS |
| **Network** | 100 Mbps |

### Software Requirements

**For Docker Installation:**
- Docker Engine 20.10+
- Docker Compose 1.29+
- Git

**For Manual Installation:**
- PHP 8.4
- MySQL 8.0
- Node.js 22.x
- Composer 2.x
- Nginx or Apache
- Git

---

## Pre-Installation Checklist

Before starting installation, ensure:

- [ ] Server/computer meets minimum requirements
- [ ] You have sudo/root access
- [ ] Ports 80, 443, 3306 are available
- [ ] Internet connection is active
- [ ] Backup strategy planned
- [ ] Domain name (if using) is configured
- [ ] SSL certificate ready (for production)

---

## Installation Methods

### Method 1: Docker (Recommended)

**Advantages:**
- Fastest setup (< 30 minutes)
- Isolated environment
- Easy to update
- Consistent across systems
- Includes all dependencies

**Use When:**
- You want quick deployment
- Running on Linux/macOS
- Multiple environments needed

### Method 2: Manual Installation

**Advantages:**
- Full control
- Better performance
- Easy integration with existing infrastructure

**Use When:**
- Docker not available
- Specific OS requirements
- Custom PHP/MySQL setup needed

---

## Docker Installation (Recommended)

### Step 1: Install Docker

**Ubuntu/Debian:**
```bash
# Update package index
sudo apt-get update

# Install prerequisites
sudo apt-get install -y \
    apt-transport-https \
    ca-certificates \
    curl \
    gnupg \
    lsb-release

# Add Docker's official GPG key
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

# Set up repository
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Install Docker Engine
sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Verify installation
sudo docker --version
sudo docker compose version
```

**Alternative (Docker Desktop):**
- Download from https://www.docker.com/products/docker-desktop
- Install and start Docker Desktop

### Step 2: Clone Repository

```bash
# Create installation directory
sudo mkdir -p /opt/blorien-pharma
cd /opt/blorien-pharma

# Clone repository
git clone https://github.com/blorien-tech/pharma.git .

# Or download release
# wget https://github.com/blorien-tech/pharma/archive/v2.6.0.tar.gz
# tar -xzf v2.6.0.tar.gz
# cd pharma-2.6.0
```

### Step 3: Configure Environment

```bash
# Copy environment file
cp app/.env.example app/.env

# Edit configuration
nano app/.env
```

**Required Settings:**
```env
APP_NAME="BLORIEN Pharma"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=blorien_pharma
DB_USERNAME=blorien_user
DB_PASSWORD=CHANGE_THIS_PASSWORD

# Pharmacy Information
PHARMACY_NAME="Your Pharmacy Name"
PHARMACY_ADDRESS="Your Address"
PHARMACY_PHONE="+880-XXX-XXXX"

# Inventory Settings
LOW_STOCK_THRESHOLD=10
EXPIRY_WARNING_DAYS=30
```

**Security Note:** Change the database password!

### Step 4: Build and Start Containers

```bash
# Build Docker images
sudo docker compose build

# Start services
sudo docker compose up -d

# Verify containers are running
sudo docker compose ps

# Expected output:
# NAME                 IMAGE           STATUS
# pharma-web-1        nginx:latest    Up
# pharma-app-1        pharma-app      Up
# pharma-db-1         mysql:8.0       Up
```

### Step 5: Install Dependencies

```bash
# Enter PHP container
sudo docker compose exec app bash

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Generate application key
php artisan key:generate

# Exit container
exit

# Install Node dependencies (from host)
cd app
npm install

# Build assets for production
npm run build

# Back to root
cd ..
```

### Step 6: Initialize Database

```bash
# Enter PHP container
sudo docker compose exec app bash

# Run migrations
php artisan migrate

# (Optional) Seed with sample data
php artisan db:seed

# Exit container
exit
```

### Step 7: Set Permissions

```bash
# Set proper ownership
sudo docker compose exec app chown -R www-data:www-data storage bootstrap/cache

# Set permissions
sudo docker compose exec app chmod -R 775 storage bootstrap/cache
```

### Step 8: Access Application

1. Open browser: `http://localhost:8000`
2. You'll see the setup page
3. Create your owner account
4. Login and start using!

---

## Manual Installation

### Step 1: Install PHP 8.4

**Ubuntu/Debian:**
```bash
# Add PHP repository
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update

# Install PHP and extensions
sudo apt-get install -y php8.4-fpm php8.4-cli php8.4-common \
    php8.4-mysql php8.4-zip php8.4-gd php8.4-mbstring \
    php8.4-curl php8.4-xml php8.4-bcmath php8.4-pdo

# Verify installation
php -v
```

### Step 2: Install MySQL 8.0

```bash
# Install MySQL
sudo apt-get install -y mysql-server

# Secure installation
sudo mysql_secure_installation

# Create database
sudo mysql -u root -p

# In MySQL:
CREATE DATABASE blorien_pharma CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'blorien_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON blorien_pharma.* TO 'blorien_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Install Node.js 22.x

```bash
# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash -
sudo apt-get install -y nodejs

# Verify installation
node -v
npm -v
```

### Step 4: Install Composer

```bash
# Download Composer
curl -sS https://getcomposer.org/installer | php

# Move to global bin
sudo mv composer.phar /usr/local/bin/composer

# Verify
composer --version
```

### Step 5: Install Nginx

```bash
# Install Nginx
sudo apt-get install -y nginx

# Create site configuration
sudo nano /etc/nginx/sites-available/blorien-pharma
```

**Nginx Configuration:**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/blorien-pharma/app/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/blorien-pharma /etc/nginx/sites-enabled/

# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

### Step 6: Deploy Application

```bash
# Create directory
sudo mkdir -p /var/www/blorien-pharma
cd /var/www/blorien-pharma

# Clone repository
sudo git clone https://github.com/blorien-tech/pharma.git .

# Set ownership
sudo chown -R www-data:www-data /var/www/blorien-pharma
sudo chmod -R 755 /var/www/blorien-pharma

# Configure environment
cd app
sudo cp .env.example .env
sudo nano .env

# Install dependencies
cd app
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Generate key
php artisan key:generate

# Run migrations
php artisan migrate

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set final permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Step 7: Configure PHP-FPM

```bash
# Edit PHP-FPM pool
sudo nano /etc/php/8.4/fpm/pool.d/www.conf

# Ensure these settings:
user = www-data
group = www-data
listen = /var/run/php/php8.4-fpm.sock
listen.owner = www-data
listen.group = www-data

# Restart PHP-FPM
sudo systemctl restart php8.4-fpm
```

---

## Initial Configuration

### Create First User

1. Navigate to: `http://your-domain.com/setup`
2. Fill in the form:
   - **Name**: Your full name
   - **Email**: Your email (will be username)
   - **Password**: Strong password (min 8 characters)
3. Click "Create Owner Account"
4. You'll be redirected to login

### Configure System Settings

Edit `.env` file:

```env
# Application
APP_NAME="Your Pharmacy Name"
APP_TIMEZONE=Asia/Dhaka

# Pharmacy Details
PHARMACY_NAME="ABC Pharmacy"
PHARMACY_ADDRESS="123 Main Street, Dhaka"
PHARMACY_PHONE="+880-1XXX-XXXXXX"
PHARMACY_EMAIL="contact@yourpharmacy.com"

# Inventory Alerts
LOW_STOCK_THRESHOLD=10
EXPIRY_WARNING_DAYS=30

# Session
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
```

After editing, clear cache:
```bash
php artisan config:cache
```

---

## Post-Installation

### Set Up Backups

**Database Backup Script:**

Create `/opt/backup-pharma.sh`:
```bash
#!/bin/bash

# Configuration
BACKUP_DIR="/backup/blorien-pharma"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="blorien_pharma"
DB_USER="blorien_user"
DB_PASS="your_password"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/blorien-pharma/app/storage

# Keep only last 30 days
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup completed: $DATE"
```

Make executable and schedule:
```bash
chmod +x /opt/backup-pharma.sh

# Add to crontab (daily at 2 AM)
crontab -e

# Add line:
0 2 * * * /opt/backup-pharma.sh >> /var/log/pharma-backup.log 2>&1
```

### Configure Firewall

```bash
# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Enable firewall
sudo ufw enable

# Check status
sudo ufw status
```

### Set Up SSL (Production)

**Using Let's Encrypt (Free):**

```bash
# Install Certbot
sudo apt-get install -y certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d your-domain.com

# Certificate auto-renewal is configured automatically
# Verify:
sudo certbot renew --dry-run
```

### Configure Logging

Edit `app/config/logging.php` or set in `.env`:

```env
LOG_CHANNEL=daily
LOG_LEVEL=warning
```

### Set Up Monitoring

**Basic Monitoring:**

```bash
# Install monitoring tools
sudo apt-get install -y htop iotop

# Monitor containers (Docker)
watch docker compose ps

# Monitor logs
sudo docker compose logs -f app

# Or for manual installation:
tail -f /var/log/nginx/error.log
tail -f /var/www/blorien-pharma/app/storage/logs/laravel.log
```

---

## Troubleshooting

### Issue: Cannot Connect to Database

**Symptoms:** "Connection refused" or "Access denied"

**Solution:**
```bash
# Check MySQL is running
sudo systemctl status mysql

# Or for Docker:
sudo docker compose ps db

# Test connection
mysql -h localhost -u blorien_user -p

# Check credentials in .env
cat app/.env | grep DB_
```

### Issue: Permission Denied

**Symptoms:** "Permission denied" when accessing pages

**Solution:**
```bash
# Docker:
sudo docker compose exec app chown -R www-data:www-data storage bootstrap/cache
sudo docker compose exec app chmod -R 775 storage bootstrap/cache

# Manual:
sudo chown -R www-data:www-data /var/www/blorien-pharma/app/storage
sudo chmod -R 775 /var/www/blorien-pharma/app/storage
```

### Issue: 502 Bad Gateway

**Symptoms:** Nginx shows 502 error

**Solution:**
```bash
# Check PHP-FPM is running
sudo systemctl status php8.4-fpm

# Restart if needed
sudo systemctl restart php8.4-fpm

# Check Nginx configuration
sudo nginx -t

# Check logs
sudo tail -f /var/log/nginx/error.log
```

### Issue: Assets Not Loading

**Symptoms:** No CSS/JS, blank page

**Solution:**
```bash
# Rebuild assets
cd app
npm run build

# Clear cache
php artisan config:clear
php artisan view:clear

# Check permissions
ls -la public/build/
```

### Issue: Migration Errors

**Symptoms:** "Table already exists" or "Column not found"

**Solution:**
```bash
# Reset database (WARNING: Deletes all data)
php artisan migrate:fresh

# Or rollback specific migration
php artisan migrate:rollback --step=1

# Check migration status
php artisan migrate:status
```

### Issue: Out of Memory

**Symptoms:** Process killed, slow performance

**Solution:**
```bash
# Increase PHP memory limit
sudo nano /etc/php/8.4/fpm/php.ini

# Change:
memory_limit = 256M

# Restart PHP-FPM
sudo systemctl restart php8.4-fpm

# For Docker, edit docker/php/Dockerfile and rebuild
```

---

## Verification Checklist

After installation, verify:

- [ ] Application accessible via browser
- [ ] Can login with owner account
- [ ] Dashboard loads correctly
- [ ] Can create a product
- [ ] Can add stock
- [ ] Can complete a sale in POS
- [ ] Language switching works
- [ ] Reports generate
- [ ] Database backup works
- [ ] Logs are being written
- [ ] SSL certificate valid (production)

---

## Uninstallation

### Docker Installation

```bash
# Stop and remove containers
sudo docker compose down

# Remove volumes (WARNING: Deletes database)
sudo docker compose down -v

# Remove images
sudo docker compose down --rmi all

# Remove project directory
sudo rm -rf /opt/blorien-pharma
```

### Manual Installation

```bash
# Stop services
sudo systemctl stop nginx
sudo systemctl stop php8.4-fpm
sudo systemctl stop mysql

# Remove application
sudo rm -rf /var/www/blorien-pharma

# Remove database
sudo mysql -u root -p
DROP DATABASE blorien_pharma;
DROP USER 'blorien_user'@'localhost';
EXIT;

# Remove Nginx configuration
sudo rm /etc/nginx/sites-enabled/blorien-pharma
sudo rm /etc/nginx/sites-available/blorien-pharma
sudo systemctl reload nginx
```

---

## Next Steps

After successful installation:

1. Read [User Manual](USER_MANUAL.md) to learn features
2. Configure pharmacy settings in `.env`
3. Add initial products and suppliers
4. Train staff on POS system
5. Set up regular backups
6. Monitor system logs

---

## Getting Help

- **Documentation:** [/docs](.)
- **Issues:** [GitHub Issues](https://github.com/blorien-tech/pharma/issues)
- **Community:** [Discussions](https://github.com/blorien-tech/pharma/discussions)

---

**Document Version:** 1.0
**System Version:** 2.6.0
**Last Updated:** November 2025

---

*End of Installation Guide*

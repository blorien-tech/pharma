# BLORIEN Pharma - Installation Guide

This guide provides detailed instructions for installing and configuring the BLORIEN Pharma system.

## System Requirements

### For Docker Installation (Recommended)
- Docker Engine 20.10+
- Docker Compose 1.29+
- 2GB RAM minimum
- 10GB disk space

### For Manual Installation
- PHP 8.4+
- MySQL 8.0+
- Nginx or Apache
- Composer
- Node.js (optional, for asset compilation)

## Docker Installation (Recommended)

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd pharma
```

### Step 2: Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Edit the .env file if needed
nano .env
```

Key environment variables:
```env
DB_DATABASE=blorien_pharma
DB_USERNAME=blorien_user
DB_PASSWORD=blorien_pass

PHARMACY_NAME="BLORIEN Pharmacy"
PHARMACY_ADDRESS="Your Pharmacy Address"
PHARMACY_PHONE="+1234567890"
```

### Step 3: Build and Start Docker Containers

```bash
# Build images
docker-compose build

# Start containers in detached mode
docker-compose up -d

# Verify containers are running
docker-compose ps
```

You should see three containers running:
- `blorien_nginx` (Web Server)
- `blorien_app` (PHP-FPM)
- `blorien_db` (MySQL)

### Step 4: Install Laravel Dependencies

```bash
# Access the PHP container
docker-compose exec app bash

# Inside the container, run:
composer install

# Generate application key
php artisan key:generate

# Exit the container
exit
```

### Step 5: Database Setup

```bash
# Access the PHP container
docker-compose exec app bash

# Run migrations
php artisan migrate

# (Optional) Seed sample data
php artisan db:seed

# Exit
exit
```

### Step 6: Set Permissions

```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Step 7: Access the Application

Open your browser and navigate to:
```
http://localhost:8000
```

You should see the setup page if this is a fresh installation.

### Step 8: Initial System Setup

1. Navigate to `http://localhost:8000/setup`
2. Create the owner account:
   - Full Name: Your name
   - Email: your-email@example.com
   - Password: (minimum 8 characters)
   - Confirm Password
3. Click "Complete Setup"
4. You will be logged in automatically

## Manual Installation (Without Docker)

### Step 1: Install PHP and Extensions

```bash
# Ubuntu/Debian
sudo apt update
sudo apt install php8.4 php8.4-fpm php8.4-mysql php8.4-xml php8.4-mbstring php8.4-zip php8.4-bcmath php8.4-gd

# Verify installation
php -v
```

### Step 2: Install MySQL

```bash
# Ubuntu/Debian
sudo apt install mysql-server
sudo mysql_secure_installation

# Create database
mysql -u root -p
CREATE DATABASE blorien_pharma;
CREATE USER 'blorien_user'@'localhost' IDENTIFIED BY 'blorien_pass';
GRANT ALL PRIVILEGES ON blorien_pharma.* TO 'blorien_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### Step 4: Clone and Setup Application

```bash
git clone <repository-url>
cd pharma/app

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Edit .env file
nano .env
```

Update these values in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blorien_pharma
DB_USERNAME=blorien_user
DB_PASSWORD=blorien_pass
```

### Step 5: Run Migrations

```bash
php artisan migrate
```

### Step 6: Configure Web Server

#### For Nginx:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/pharma/app/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Step 7: Set Permissions

```bash
sudo chown -R www-data:www-data /path/to/pharma/app
sudo chmod -R 775 /path/to/pharma/app/storage
sudo chmod -R 775 /path/to/pharma/app/bootstrap/cache
```

### Step 8: Start Services

```bash
sudo systemctl start nginx
sudo systemctl start php8.4-fpm
sudo systemctl start mysql
```

## Post-Installation

### 1. Access the Application

Navigate to your configured domain or `http://localhost`

### 2. Complete System Setup

- Go to `/setup` route
- Create your owner account
- Configure pharmacy details in `.env` file

### 3. Create Additional Users

- Log in as owner
- Navigate to Users → Create User
- Add Manager and Cashier accounts

### 4. Add Products

- Navigate to Products → Add Product
- Fill in product details
- Add batches with expiry dates

### 5. Start Using POS

- Navigate to POS
- Search for products
- Process your first sale!

## Troubleshooting

### Cannot Access Application

**Check containers:**
```bash
docker-compose ps
docker-compose logs
```

**Restart containers:**
```bash
docker-compose down
docker-compose up -d
```

### Database Connection Error

**Check MySQL is running:**
```bash
docker-compose exec db mysql -u blorien_user -p
```

**Verify credentials in `.env`**

### Permission Errors

```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Clear Application Cache

```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
```

## Updating the Application

```bash
# Pull latest changes
git pull origin main

# Update dependencies
docker-compose exec app composer install

# Run new migrations
docker-compose exec app php artisan migrate

# Clear cache
docker-compose exec app php artisan cache:clear
```

## Backup and Restore

### Backup Database

```bash
docker-compose exec db mysqldump -u blorien_user -p blorien_pharma > backup.sql
```

### Restore Database

```bash
docker-compose exec -T db mysql -u blorien_user -p blorien_pharma < backup.sql
```

## Production Deployment

For production deployment:

1. **Set APP_ENV to production** in `.env`
2. **Set APP_DEBUG to false**
3. **Use strong passwords** for database
4. **Enable HTTPS** with SSL certificates
5. **Set up regular backups**
6. **Configure firewall rules**
7. **Enable error logging**
8. **Optimize autoloader**:
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## Support

For issues during installation:
1. Check logs: `docker-compose logs -f app`
2. Review error messages
3. Create an issue in the repository with:
   - Error message
   - Steps to reproduce
   - System information

## Next Steps

After successful installation, refer to:
- [User Guide](USER_GUIDE.md) - Learn how to use the system
- [API Documentation](API_DOCUMENTATION.md) - For developers

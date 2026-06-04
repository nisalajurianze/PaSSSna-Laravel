# PaSSSna Restaurant Management System - Setup Guide

## Overview

PaSSSna is a fully functional Laravel-based Restaurant Management System with customer and admin panels, featuring menu management, orders, reservations, inventory, staff management, and reports.

## Requirements

- PHP 8.1 or higher
- Composer
- MySQL 8.0+
- Node.js & npm
- XAMPP (or any MySQL server)

## Installation Steps

### 1. Clone/Download the Project

Extract the project to your web directory or desired location.

### 2. Install PHP Dependencies

```bash
cd PaSSSna-Restaurant
composer install
```

### 3. Install NPM Dependencies

```bash
npm install
```

### 4. Configure Environment

Copy the example environment file and configure your settings:

```bash
copy .env.example .env
```

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=passsna_restaurant
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Run Database Migrations

```bash
php artisan migrate --seed
```

This will create all necessary tables and seed initial data including:
- Admin user
- Sample menu items
- Tables
- Promotions

### 7. Create Storage Link

```bash
php artisan storage:link
```

### 8. Build Assets

```bash
npm run build
```

### 9. Start Development Servers

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```

**Terminal 2 - Vite (Optional for development):**
```bash
npm run dev
```

## Default Admin Credentials

- **Email:** admin.passsna@gmail.com
- **Password:** PaSSSna_log

## Project Structure

```
PaSSSna-Restaurant/
├── app/
│   ├── Console/Commands/       # Custom Artisan commands
│   ├── Exceptions/             # Custom exception handlers
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin panel controllers
│   │   │   ├── Auth/           # Authentication controllers
│   │   │   └── Customer/       # Customer panel controllers
│   │   └── Middleware/         # Custom middleware
│   ├── Models/                 # Eloquent models
│   ├── Services/               # Business logic services
│   └── Providers/              # Service providers
├── database/
│   ├── migrations/             # Database migrations
│   └── seeders/                # Database seeders
├── resources/views/
│   ├── admin/                  # Admin panel views
│   ├── auth/                   # Authentication views
│   ├── customer/               # Customer panel views
│   ├── layouts/                # Blade layouts
│   └── pdf/                    # PDF templates
├── routes/
│   └── web.php                 # Web routes
└── config/                     # Configuration files
```

## Key Features

### Customer Panel
- User registration & login
- Menu browsing with search & filters
- Shopping cart
- Checkout with multiple payment methods
- Order tracking
- Reservations
- Dining section with custom meal builder

### Admin Panel
- Dashboard with KPIs and charts
- Menu management (CRUD)
- Orders management (delivery, takeaway, dining)
- Reservations management
- Inventory management
- Staff management
- Promotions management
- Reports with PDF export

## Database Tables

- `users` - Customer and admin accounts
- `menu_items` - Menu items with categories, prices, images
- `orders` - Customer orders
- `order_items` - Individual items in orders
- `reservations` - Table reservations
- `tables` - Restaurant tables
- `inventory` - Kitchen stock items
- `staff` - Employee records
- `promotions` - Discount codes and offers
- `reviews` - Customer reviews
- `contact_messages` - Contact form submissions

## Customization

### Theme Colors

Edit `tailwind.config.js` to customize colors:

```javascript
colors: {
  'primary-red': '#DC2626',
  'primary-yellow': '#FBBF24',
  'navy-blue': '#1E3A8A',
}
```

### Restaurant Settings

Edit `.env` file or `config/restaurant.php` for:
- Restaurant name
- Operating hours
- Delivery settings
- Tax rates
- Payment gateway settings

## Common Issues & Solutions

### 1. Database Connection Error
- Ensure MySQL is running
- Check database credentials in `.env`
- Create the database first: `CREATE DATABASE passsna_restaurant;`

### 2. Class Not Found Errors
- Run `composer dump-autoload`
- Clear caches: `php artisan optimize:clear`

### 3. Images Not Loading
- Run `php artisan storage:link`
- Check file permissions on `storage/public` folder

### 4. Migration Errors
- Reset migrations: `php artisan migrate:fresh --seed`

## Production Deployment

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Run `php artisan optimize`
4. Configure proper web server (Apache/Nginx)
5. Set up SSL certificate

## Support

For issues or questions, please check:
1. Laravel documentation: https://laravel.com/docs
2. Tailwind CSS documentation: https://tailwindcss.com/docs

## License

This project is open-sourced software licensed under the MIT license.

# Store Order & Inventory Mini-System

A robust full-stack order management and inventory tracking system built with Laravel 11, Tailwind CSS, and Alpine.js. It features a normalized relational database, concurrency-safe inventory checks via row-level locking, a clean service-layer architecture, and asynchronous background job processing.

## Tech Stack
- Laravel 11, PHP 8.2+, MySQL
- Frontend: Tailwind CSS & Alpine.js
- Queue driver: database (or sync for local development)

## Setup
```bash
git clone https://github.com/kumar0602/store-inventory-system.git
cd store-inventory-system
composer install
copy .env.example .env
php artisan key:generate

# Configure your database credentials (DB_DATABASE, DB_USERNAME, DB_PASSWORD) in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=store_order
DB_USERNAME=root
DB_PASSWORD=

php artisan migrate:fresh --seed
php artisan queue:work      
php artisan serve

Once running, open your browser at: http://127.0.0.1:8001/billing

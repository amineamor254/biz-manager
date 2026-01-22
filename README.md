# Business Manager Dashboard

A Laravel-based application to manage clients, products, and invoices with authentication and a dynamic dashboard.

## Features
- Authentication (Login / Register)
- Clients CRUD
- Products CRUD
- Invoices CRUD
- Dynamic Dashboard
- Monthly Income Chart
- Tailwind CSS UI

## Tech Stack
- Laravel
- MySQL
- Blade
- Tailwind CSS
- Chart.js

## Installation
```bash
git clone https://github.com/amineamor254/business-manager-laravel.git
cd business-manager-laravel
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve

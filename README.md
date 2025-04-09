# Laravel Inventory App

A simple Laravel-based inventory management application.

## Features
- CRUD for products, categories, and suppliers
- Real-time stock notification with Pusher
- WhatsApp alerts for low stock
- Barcode system for inventory scanning

## Requirements
- PHP >= 8.1
- Composer
- MySQL or PostgreSQL
- Node.js & NPM

## Installation

```bash
git clone https://github.com/HexxaZen/inventoryapp.git
cd inventoryapp
cp .env.example .env
composer install
npm install && npm run dev
php artisan key:generate
php artisan migrate --seed

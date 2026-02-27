# Solusi Kopi

Web application for cafe and restaurant operations, built with Laravel.
This project covers customer ordering via table QR, cashier payment flow, and admin reporting.

## Portfolio Scope

- QR-based table ordering with guest flow.
- Midtrans QRIS integration and cash payment handling.
- Role-based backoffice for admin and cashier operations.
- Reporting dashboard for sales recap and operational monitoring.

## Main Features

### Customer Side

- Scan table QR and open menu by table code.
- Add/remove items in cart with live total calculation.
- Checkout flow with QRIS and payment status tracking.
- Order history and order detail pages.

### Backoffice Side

- Product, category, table, outlet, and promotion management.
- Payment validation page for cashier/admin.
- Order management with status update workflow.
- Reporting page with export support.

## Tech Stack

- Backend: Laravel 10, PHP 8.1+
- Frontend: Blade, Livewire 3, Vite
- Database: MySQL/PostgreSQL
- Auth and roles: Laravel Sanctum, Spatie Permission
- Payment gateway: Midtrans (QRIS)

## Role Access

- `admin`: full access to console modules and reporting.
- `kasir` / `cashier`: payment handling and operational reporting.
- `user` / `costumer`: customer-level access.

## Quick Start

### 1. Clone and install dependencies

```bash
git clone https://github.com/Ice192/solusiKopi.git
cd solusi-kopi
composer install
npm install
```

### 2. Create environment file

```bash
cp .env.example .env
# PowerShell alternative:
Copy-Item .env.example .env
php artisan key:generate
```

### 3. Configure database and Midtrans in `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=solusi_kopi
DB_USERNAME=root
DB_PASSWORD=

MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_MERCHANT_ID=your_merchant_id
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

For complete Midtrans setup details, see `MIDTRANS_SETUP.md`.

### 4. Run migration, seeders, and assets

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
npm run dev
php artisan serve
```

Open `http://127.0.0.1:8000`.

## Demo Accounts (Seeder)

- Admin: `admin@mail.com` / `password`
- Kasir: `kasir@mail.com` / `password`
- Cashier: `cashier@mail.com` / `password`
- User: `user@mail.com` / `password`
- Costumer: `costumer@mail.com` / `password`

## Testing

```bash
php artisan test
```

## Selected Project Structure

```text
app/
  Http/Controllers/
    OrderController.php
    DashboardController.php
    Console/
      OrderManagementController.php
      PaymentController.php
      ReportingController.php
  Livewire/
    MenuLivewire.php
resources/views/
  order/
  console/
```

## Public Repo Safety Notes

- `.env` is not tracked in Git.
- Keep API keys and credentials only in local/server environment variables.
- Rotate secrets immediately if they were ever exposed.

## Status

Active development.


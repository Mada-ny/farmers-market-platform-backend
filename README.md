# Farmers Market Platform — Backend API

RESTful API backend for an agricultural marketplace in Côte d'Ivoire.
POS operators manage farmer accounts, place product orders on their behalf, and record commodity repayments against credit debts.

Built with **Laravel 11** · **PHP 8.2** · **MySQL** · **Laravel Sanctum**

---

## Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+

---

## Setup

```bash
# 1. Clone the repository
git clone <repo-url>
cd farmers-market-platform-backend

# 2. Install dependencies
composer install

# 3. Configure environment
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials:

```ini
DB_DATABASE=farmers_market_platform
DB_USERNAME=root
DB_PASSWORD=your_password
```

```bash
# 4. Run migrations and seed demo data
php artisan migrate --seed

# 5. Start the development server
php artisan serve
```

The API is now available at `http://localhost:8000/api/v1`.

---

## Demo Accounts

Seeded by `php artisan db:seed`:

| Role       | Email                        | Password    |
|------------|------------------------------|-------------|
| Admin      | admin@farmmarket.ci          | Admin1234!  |
| Supervisor | superviseur@farmmarket.ci    | Super1234!  |
| Operator   | operateur@farmmarket.ci      | Oper1234!   |

The seeder also creates 33 categories, 26 products, 15 Ivorian farmer profiles, sample transactions (cash and credit), and repayments demonstrating FIFO debt settlement.

---

## API Documentation

Import `postman_collection.json` from the repository root into Postman.

The collection includes all endpoints with example requests and responses. Run the **Login** request first — it automatically stores the token in a collection variable used by every other request.

**Base URL:** `http://localhost:8000/api/v1`

### Endpoint Summary

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/auth/login` | Obtain a Sanctum token |
| POST | `/auth/logout` | Revoke the current token |
| GET/POST | `/users` | List / create users |
| GET/PUT/DELETE | `/users/{id}` | Read / update / delete a user |
| GET/POST | `/categories` | List / create categories |
| GET/PUT/DELETE | `/categories/{id}` | Read / update / delete a category |
| GET/POST | `/products` | List / create products |
| GET/PUT/DELETE | `/products/{id}` | Read / update / delete a product |
| GET/POST | `/farmers` | List / create farmers (`?search=` for identifier or phone lookup) |
| GET/PUT/DELETE | `/farmers/{id}` | Read / update / delete a farmer |
| GET | `/farmers/{id}/debts` | List a farmer's outstanding debts |
| GET/POST | `/transactions` | List / create transactions |
| GET | `/transactions/{id}` | Read a transaction |
| GET/POST | `/repayments` | List / create repayments |
| GET | `/repayments/{id}` | Read a repayment |

---

## Role Permissions

| Action | Admin | Supervisor | Operator |
|--------|-------|------------|----------|
| Manage supervisors | ✅ | ❌ | ❌ |
| Manage operators | ❌ | ✅ | ❌ |
| Manage products & categories | ✅ | ✅ | ❌ |
| View products & categories | ✅ | ✅ | ✅ |
| Manage farmers | ❌ | ❌ | ✅ |
| Place transactions | ❌ | ❌ | ✅ |
| Record repayments | ❌ | ❌ | ✅ |

---

## Key Business Rules

- **Credit transactions**: interest is applied — `credited_amount = total_fcfa × (1 + interest_rate)`. The default rate is `0.10` (10%); pass `interest_rate` in the request to override.
- **Credit limit**: a transaction is blocked (422) if the new debt would push the farmer's total outstanding debt above their `credit_limit`.
- **FIFO repayment**: when a farmer repays with commodities, the oldest unpaid debt is settled first. Partial repayments leave the remaining balance open.
- **Commodity rate**: passed per repayment request (`commodity_rate` in FCFA/kg); locked at the time of recording.

---

## Running Tests

```bash
php artisan test
```

67 feature tests covering auth, role enforcement, credit limit, FIFO repayment, partial repayment, pagination, filters, and data visibility scoping.

---

## Common Commands

```bash
composer run dev        # Start server + queue listener + log tail
php artisan migrate     # Run migrations
php artisan db:seed     # Seed demo data
php artisan test        # Run all tests
./vendor/bin/pint       # Format code (Laravel Pint)
php artisan tinker      # Interactive REPL
```

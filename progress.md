# Project Progress

## Done

### Foundation

- [x] Laravel 11 + Sanctum setup
- [x] MySQL database configured (`farmers_market_platform`)
- [x] `CLAUDE.md` and `.claude/rules/` reference docs
- [x] `Role` and `PaymentMethod` backed enums
- [x] 9 migrations (users + role, categories, products, farmers, transactions, transaction_items, debts, repayments, repayment_debt)
- [x] All Eloquent models with explicit `$fillable`, casts, and typed relationships
- [x] Global JSON error handler (401, 403, 404, 422, 500)
- [x] `RoleMiddleware` with alias registration

### Auth

- [x] `POST /api/v1/auth/login` — returns Sanctum token
- [x] `POST /api/v1/auth/logout` — revokes current token

### Users

- [x] `GET /api/v1/users`
- [x] `POST /api/v1/users`
- [x] `GET /api/v1/users/{id}`
- [x] `PUT /api/v1/users/{id}`
- [x] `DELETE /api/v1/users/{id}`

### Categories

- [x] `GET /api/v1/categories`
- [x] `POST /api/v1/categories`
- [x] `GET /api/v1/categories/{id}`
- [x] `PUT /api/v1/categories/{id}`
- [x] `DELETE /api/v1/categories/{id}`

### Products

- [x] `GET /api/v1/products`
- [x] `POST /api/v1/products`
- [x] `GET /api/v1/products/{id}`
- [x] `PUT /api/v1/products/{id}`
- [x] `DELETE /api/v1/products/{id}`

### Farmers

- [x] `GET /api/v1/farmers`
- [x] `POST /api/v1/farmers`
- [x] `GET /api/v1/farmers/{id}`
- [x] `PUT /api/v1/farmers/{id}`
- [x] `DELETE /api/v1/farmers/{id}`
- [x] `GET /api/v1/farmers/{id}/debts` — outstanding debts ordered by date (FIFO)

### Transactions

- [x] `GET /api/v1/transactions`
- [x] `POST /api/v1/transactions` — cash and credit (interest + credit limit enforcement)
- [x] `GET /api/v1/transactions/{id}`
- [x] Debt automatically created on credit transactions
- [x] Credit limit check before any credit transaction

### Repayments

- [x] `POST /api/v1/repayments` — FIFO allocation across outstanding debts
- [x] Blocked when farmer has no outstanding debt (422)
- [x] Partial repayments supported (debt stays open until fully settled)

### Quality

- [x] 44 feature tests (auth, role enforcement, FIFO, partial repayment, credit limit, cascade, pagination, filters, farmer insights)
- [x] Realistic seeders (3 users, 33 categories, 26 products, 15 Ivorian farmers)
- [x] Postman collection (`postman_collection.json`)
- [x] PR #2 open on `feature/foundation-api`

---

## To Do

### Pagination & Filtering

- [x] Paginate all listing endpoints (`users`, `categories`, `products`, `farmers`, `transactions`) with `per_page` param
- [x] Filter transactions by `farmer_id`, `payment_method`, `date_from`, `date_to`
- [x] Filter products by `category_id`

### Farmer Insights

- [x] Expose `outstanding_debt` (sum of `remaining_amount`) on `FarmerResource`
- [x] Expose `available_credit` (`credit_limit - outstanding_debt`) on `FarmerResource`

### Repayment History

- [ ] `GET /api/v1/repayments` — list repayments (filterable by farmer)
- [ ] `GET /api/v1/repayments/{id}` — detail with debts settled

### Interest Rate Config

- [ ] Store a default interest rate in `config/` or a settings table
- [ ] Make `interest_rate` optional on credit transactions (falls back to default)

### Security Hardening

- [ ] Scope `GET /api/v1/transactions` to the authenticated operator's own transactions
- [ ] Prevent an operator from creating a transaction for a farmer that belongs to another operator's zone (if applicable)

### Testing

- [x] Tests for pagination and filter params
- [ ] Tests for `outstanding_debt` and `available_credit` on farmer
- [ ] Tests for repayment history endpoints

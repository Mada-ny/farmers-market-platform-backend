# Database Schema

## Tables

### users

| Column      | Type                              | Notes                        |
|-------------|-----------------------------------|------------------------------|
| id          | bigIncrements                     |                              |
| name        | string                            |                              |
| email       | string, unique                    |                              |
| password    | string                            |                              |
| role        | enum(admin, supervisor, operator) |                              |
| timestamps  |                                   |                              |

### categories

| Column      | Type                | Notes                              |
|-------------|---------------------|------------------------------------|
| id          | bigIncrements       |                                    |
| name        | string              |                                    |
| parent_id   | foreignId, nullable | Self-referencing, null = root      |
| timestamps  |                     |                                    |

### products

| Column      | Type          | Notes                    |
|-------------|---------------|--------------------------|
| id          | bigIncrements |                          |
| name        | string        |                          |
| description | text, nullable|                          |
| price       | decimal(10,2) | FCFA                     |
| category_id | foreignId     | FK → categories          |
| timestamps  |               |                          |

### farmers

| Column       | Type          | Notes                        |
|--------------|---------------|------------------------------|
| id           | bigIncrements |                              |
| identifier   | string, unique| From farmer card             |
| firstname    | string        |                              |
| lastname     | string        |                              |
| phone        | string, unique|                              |
| credit_limit | decimal(10,2) | Max debt allowed in FCFA     |
| timestamps   |               |                              |

### transactions

| Column          | Type                    | Notes                           |
|-----------------|-------------------------|---------------------------------|
| id              | bigIncrements           |                                 |
| farmer_id       | foreignId               | FK → farmers                    |
| operator_id     | foreignId               | FK → users                      |
| total_fcfa      | decimal(10,2)           | Cash total before interest      |
| payment_method  | enum(cash, credit)      |                                 |
| interest_rate   | decimal(5,2), nullable  | Only for credit transactions    |
| credited_amount | decimal(10,2), nullable | total_fcfa × (1 + interest_rate)|
| timestamps      |                         |                                 |

### transaction_items

| Column         | Type           | Notes                  |
|----------------|--------------- |------------------------|
| id             | bigIncrements  |                        |
| transaction_id | foreignId      | FK → transactions      |
| product_id     | foreignId      | FK → products          |
| quantity       | unsignedInteger|                        |
| unit_price     | decimal(10,2)  | Price at time of sale  |
| timestamps     |                |                        |

### debts

| Column           | Type          | Notes                          |
|------------------|---------------|--------------------------------|
| id               | bigIncrements |                                |
| transaction_id   | foreignId     | FK → transactions              |
| farmer_id        | foreignId     | FK → farmers                   |
| amount_fcfa      | decimal(10,2) | Original debt amount           |
| remaining_amount | decimal(10,2) | Decreases as repayments apply  |
| timestamps       |               |                                |

### repayments

| Column         | Type          | Notes                              |
|----------------|---------------|------------------------------------|
| id             | bigIncrements |                                    |
| farmer_id      | foreignId     | FK → farmers                       |
| operator_id    | foreignId     | FK → users                         |
| kg_received    | decimal(10,2) | Commodity weight received          |
| commodity_rate | decimal(10,2) | FCFA per kg at time of repayment   |
| fcfa_value     | decimal(10,2) | kg_received × commodity_rate       |
| timestamps     |               |                                    |

### repayment_debt (pivot)

| Column         | Type          | Notes                              |
|----------------|---------------|------------------------------------|
| id             | bigIncrements |                                    |
| repayment_id   | foreignId     | FK → repayments                    |
| debt_id        | foreignId     | FK → debts                         |
| amount_applied | decimal(10,2) | Amount credited to this debt       |
| timestamps     |               |                                    |

## Eloquent Relationships

```php
// User
public function supervisedOperators(): HasMany // role = operator
public function transactions(): HasMany
public function repayments(): HasMany

// Category
public function parent(): BelongsTo
public function children(): HasMany

// Product
public function category(): BelongsTo

// Farmer
public function transactions(): HasMany
public function debts(): HasMany
public function repayments(): HasMany

// Transaction
public function farmer(): BelongsTo
public function operator(): BelongsTo
public function items(): HasMany
public function debt(): HasOne

// TransactionItem
public function transaction(): BelongsTo
public function product(): BelongsTo

// Debt
public function transaction(): BelongsTo
public function farmer(): BelongsTo
public function repayments(): BelongsMany // through repayment_debt

// Repayment
public function farmer(): BelongsTo
public function operator(): BelongsTo
public function debts(): BelongsToMany // through repayment_debt
```

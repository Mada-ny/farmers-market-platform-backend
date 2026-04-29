# Business Rules

## 1. Credit Transaction

When payment_method is `credit`:

- Apply interest to the cash total: `credited_amount = total_fcfa × (1 + interest_rate)`
- `interest_rate` is configurable (stored in config or a settings table)
- Create a `Debt` record with `amount_fcfa = credited_amount` and `remaining_amount = credited_amount`
- Cash transactions never create a debt

## 2. Credit Limit Enforcement

Before creating a credit transaction:

- Calculate the farmer's current total outstanding debt:
  `SELECT SUM(remaining_amount) FROM debts WHERE farmer_id = ? AND remaining_amount > 0`
- Add the new `credited_amount` to the current total
- If the result exceeds `farmer.credit_limit` → **block the transaction**, return 422

```php
$outstandingDebt = $farmer->debts()->where('remaining_amount', '>', 0)->sum('remaining_amount');

if ($outstandingDebt + $creditedAmount > $farmer->credit_limit) {
    throw new CreditLimitExceededException();
}
```

## 3. Repayment — FIFO

When a repayment is recorded:

- Convert kg to FCFA: `fcfa_value = kg_received × commodity_rate`
- Fetch the farmer's unpaid debts ordered by `created_at ASC` (oldest first)
- Apply `fcfa_value` across debts in order until exhausted

```php
$remaining = $fcfaValue;

$debts = Debt::where('farmer_id', $farmer->id)
    ->where('remaining_amount', '>', 0)
    ->orderBy('created_at', 'asc')
    ->get();

foreach ($debts as $debt) {
    if ($remaining <= 0) break;

    $applied = min($remaining, $debt->remaining_amount);

    $debt->decrement('remaining_amount', $applied);

    RepaymentDebt::create([
        'repayment_id'   => $repayment->id,
        'debt_id'        => $debt->id,
        'amount_applied' => $applied,
    ]);

    $remaining -= $applied;
}
```

## 4. Partial Repayment

- If `fcfa_value` does not cover a debt fully, `remaining_amount` is reduced but stays > 0
- The debt remains open until fully settled
- No debt is ever deleted — only `remaining_amount` reaches 0

## 5. Commodity Rate

- Configurable per repayment — stored at time of recording in `repayments.commodity_rate`
- Never recalculate retroactively — rate is locked at repayment time
- `fcfa_value = kg_received × commodity_rate`

## 6. Role Hierarchy & Permissions

| Action                        | Admin | Supervisor | Operator |
|-------------------------------|-------|------------|----------|
| Manage supervisors            | ✅    | ❌         | ❌       |
| Manage operators              | ❌    | ✅         | ❌       |
| Manage products & categories  | ✅    | ✅         | ❌       |
| Manage farmers                | ❌    | ❌         | ✅       |
| Place transactions            | ❌    | ❌         | ✅       |
| Record repayments             | ❌    | ❌         | ✅       |

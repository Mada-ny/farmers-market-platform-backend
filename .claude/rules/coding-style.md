# Coding Style

## General

- `declare(strict_types=1)` on every PHP file, no exceptions
- Use PHP 8.2 features: typed properties, enums, readonly where appropriate
- No comments unless logic is non-obvious — code should be self-explanatory

## Controllers

Controllers are thin. They validate, delegate, and return. Nothing else.

### Good

```php
<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService
    ) {}

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $transaction = $this->transactionService->create($request->validated());

        return TransactionResource::make($transaction)
            ->response()
            ->setStatusCode(201);
    }
}
```

### Bad (do NOT follow)

```php
// WRONG: business logic in controller, no FormRequest, raw model return
public function store(Request $request)
{
    $data = $request->all();
    $total = 0;
    foreach ($data['items'] as $item) {
        $total += $item['price'] * $item['qty'];
    }
    $transaction = Transaction::create([...$data, 'total' => $total]);
    return response()->json($transaction);
}
```

## Models

- Always define `$fillable` explicitly — never use `$guarded = []`
- Use Enums for status and type fields, not strings
- Relationships must have explicit return types
- Scopes must be typed and named descriptively

### Good

```php
<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'farmer_id',
        'operator_id',
        'total_fcfa',
        'payment_method',
        'interest_rate',
        'credited_amount',
    ];

    protected function casts(): array
    {
        return [
            'payment_method' => PaymentMethod::class,
            'total_fcfa' => 'decimal:2',
            'credited_amount' => 'decimal:2',
        ];
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }
}
```

## Services

- One service per domain (TransactionService, RepaymentService, etc.)
- Injected via constructor, never instantiated with `new`
- Accept only validated data (arrays from `$request->validated()`)
- Return Eloquent models or collections — never raw arrays

## Enums

- Use PHP 8.1+ backed enums for all fixed value sets
- Never use plain strings for roles, payment methods, or statuses

### Good

```php
<?php
declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Credit = 'credit';
}

enum Role: string
{
    case Admin = 'admin';
    case Supervisor = 'supervisor';
    case Operator = 'operator';
}
```

## SOLID / DRY / KISS

- **Single Responsibility**: one class, one job
- **Open/Closed**: extend behavior via new classes, not by modifying existing ones
- **Dependency Injection**: always inject, never instantiate
- **DRY**: extract repeated logic into shared methods or services
- **KISS**: if it needs a long explanation, simplify it

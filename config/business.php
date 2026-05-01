<?php

declare(strict_types=1);

return [
    // Percentage (10 = 10%). Consistent with the API convention.
    // Divided by 100 inside TransactionService before calculations.
    'interest_rate' => (float) env('DEFAULT_INTEREST_RATE', 10),
];

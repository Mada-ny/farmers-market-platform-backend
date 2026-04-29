<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class NoOutstandingDebtException extends Exception
{
    public function __construct()
    {
        parent::__construct('This farmer has no outstanding debt to repay.');
    }
}

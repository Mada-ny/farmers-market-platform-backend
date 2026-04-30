<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Supervisor = 'supervisor';
    case Operator = 'operator';

    public function manageableRole(): self
    {
        return match ($this) {
            self::Admin => self::Supervisor,
            self::Supervisor => self::Operator,
            self::Operator => throw new \LogicException('Operators cannot manage users'),
        };
    }
}

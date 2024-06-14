<?php

namespace App;

enum RolesEnum: string
{
    case Admin = 'admin';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            static::Admin => 'Administrators',
            static::Customer => 'Customers',
        };
    }
}

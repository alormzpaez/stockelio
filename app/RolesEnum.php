<?php

namespace App;

enum RolesEnum: string
{
    case Admin = 'admin';
    case Customer = 'customer';
    case TechnicalSupportSpecialist = 'technical support specialist';

    public function label(): string
    {
        return match ($this) {
            static::Admin => 'Administrators',
            static::Customer => 'Customers',
            static::TechnicalSupportSpecialist => 'Technichal Support Specialists',
        };
    }
}

<?php

namespace App;

enum PermissionsEnum: string
{
    case UpdateProduct = 'update product';

    public function label(): string
    {
        return match ($this) {
            static::UpdateProduct => 'Ability to watch edit form and update one product',
        };
    }
}

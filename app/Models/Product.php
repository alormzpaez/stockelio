<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    public $incrementing = false;

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    public function cheapestVariant(): HasOne
    {
        return $this->variants()->one()->ofMany('retail_price', 'min');
    }
}

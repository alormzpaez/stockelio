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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'thumbnail_url',
        'description',
        'stripe_product_id',
    ];

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    public function cheapestVariant(): HasOne
    {
        return $this->variants()->one()->ofMany('retail_price', 'min');
    }
}

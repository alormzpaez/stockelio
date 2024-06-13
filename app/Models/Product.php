<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'thumbnail_url',
        'description',
        'printful_product_id',
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

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }
}

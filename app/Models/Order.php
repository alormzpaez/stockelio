<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    public const IN_CART_STATUS = 'incart';
    public const DRAFT_STATUS = 'draft';
    public const PENDING_STATUS = 'pending';
    public const FULFILLED_STATUS = 'fulfilled';
    public const STATUSES = [
        self::IN_CART_STATUS,
        self::DRAFT_STATUS,
        self::PENDING_STATUS,
        self::FULFILLED_STATUS,
    ];

    protected $attributes = [
        'status' => self::IN_CART_STATUS,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'variant_id',
        'status',
        'quantity',
        'printful_order_id',
    ];
    
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }
}

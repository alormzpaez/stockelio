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
    public const FULFILLED_STATUS = 'fulfilled';
    public const STATUSES = [
        self::IN_CART_STATUS,
        self::DRAFT_STATUS,
        self::FULFILLED_STATUS,
    ];

    protected $attributes = [
        'status' => self::IN_CART_STATUS,
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

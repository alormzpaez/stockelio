<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_preferred' => 'boolean',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'is_preferred' => false,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'is_preferred',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['full_address'];

    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: fn () => 
                $this->address . ', ' .
                $this->locality . '. ' .
                $this->city . ', ' .
                $this->state_name . '. C.P.: ' .
                $this->zip
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

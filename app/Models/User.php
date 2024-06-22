<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Observers\UserObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[ObservedBy(UserObserver::class)]
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, Billable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['personalData'];

    /**
     * Useful as 'recipient' when create a new order in Printful API.
     */
    protected function personalData(): Attribute
    {
        return Attribute::make(
            get: fn () => [
                'name' => $this->name,
                'address1' => $this->address1,
                'city' => $this->city,
                'state_code' => $this->state_code,
                'country_code' => $this->country_code,
                'zip' => $this->zip,
            ]
        );
    }

    protected $with = ['cart'];

    public function setNewPreferredLocation(int $locationId): void
    {
        $this->load('locations');

        $this->locations->each(fn (Location $location) =>
            $location->update([
                'is_preferred' => ($location->id == $locationId)
            ])
        );
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function preferredLocation(): HasOne
    {
        return $this->locations()->one()->where('is_preferred', true);
    }
}

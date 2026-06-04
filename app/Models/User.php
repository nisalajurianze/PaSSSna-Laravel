<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'payment_card_last_four',
        'payment_card_type',
        'payment_card_expiry',
        'is_active',
        'email_verified_at',
        'preferences',
        'avatar',
        'last_login_at',
        'last_login_ip'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'preferences' => 'array',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function contactMessages()
    {
        return $this->hasMany(ContactMessage::class);
    }

    public function diningSessions()
    {
        return $this->hasMany(DiningSession::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Scopes
    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function getFormattedAddressAttribute()
    {
        return nl2br($this->address);
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return substr($initials, 0, 2);
    }

    // Loyalty Points (computed)
    public function getLoyaltyPointsAttribute()
    {
        // 10 points per completed order
        $totalPoints = $this->orders()
            ->where('status', 'completed')
            ->count() * 10;

        // Subtract points already used in redemptions
        $usedPoints = \App\Models\LoyaltyRedemption::where('user_id', $this->id)
            ->whereIn('status', [\App\Models\LoyaltyRedemption::STATUS_PENDING, \App\Models\LoyaltyRedemption::STATUS_USED])
            ->sum('points_used');

        return $totalPoints - $usedPoints;
    }
}

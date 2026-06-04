<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyRedemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loyalty_reward_id',
        'promo_code',
        'points_used',
        'status',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    // Statuses
    public const STATUS_PENDING = 'pending';
    public const STATUS_USED = 'used';
    public const STATUS_EXPIRED = 'expired';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reward()
    {
        return $this->belongsTo(LoyaltyReward::class, 'loyalty_reward_id');
    }

    public function isUsed(): bool
    {
        return $this->status === self::STATUS_USED;
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    // Scopes
    public function scopeUsed($query)
    {
        return $query->where('status', self::STATUS_USED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}

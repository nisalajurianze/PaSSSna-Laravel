<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image_url',
        'points_required',
        'reward_type',
        'reward_value',
        'minimum_order_amount',
        'max_uses',
        'current_uses',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'reward_value' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'valid_until' => 'date',
    ];

    // Reward types
    public const TYPE_DISCOUNT_PERCENT = 'discount_percent';
    public const TYPE_DISCOUNT_AMOUNT = 'discount_amount';
    public const TYPE_FREE_ITEM = 'free_item';

    // Relationships
    public function redemptions()
    {
        return $this->hasMany(LoyaltyRedemption::class);
    }

    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->valid_until && \Carbon\Carbon::parse($this->valid_until)->isPast()) {
            return false;
        }

        if ($this->max_uses !== null && $this->current_uses >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function getRemainingUsesAttribute(): ?int
    {
        if ($this->max_uses === null) {
            return null;
        }

        return $this->max_uses - $this->current_uses;
    }

    public function getFormattedRewardAttribute(): string
    {
        return match ($this->reward_type) {
            self::TYPE_DISCOUNT_PERCENT => $this->reward_value . '% OFF',
            self::TYPE_DISCOUNT_AMOUNT => '$' . number_format((float) $this->reward_value, 2) . ' OFF',
            self::TYPE_FREE_ITEM => 'Free Item',
            default => 'Reward',
        };
    }

    public function getFormattedMinimumOrderAttribute(): string
    {
        if ($this->minimum_order_amount <= 0) {
            return 'No minimum';
        }

        return 'Min. order $' . number_format((float) $this->minimum_order_amount, 2);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_until')
                    ->orWhere('valid_until', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('max_uses')
                    ->orWhereColumn('current_uses', '<', 'max_uses');
            });
    }

    public function scopeByPointsAsc($query)
    {
        return $query->orderBy('points_required', 'asc');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'promo_code',
        'code',
        'name',
        'description',
        'type',
        'discount_type',
        'discount_value',
        'minimum_order_amount',
        'maximum_uses',
        'uses_per_customer',
        'times_used',
        'start_date',
        'end_date',
        'valid_from',
        'valid_to',
        'is_active',
        'is_visible',
        'applicable_categories',
        'excluded_items',
        'free_item_id',
        'free_item_quantity',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'maximum_uses' => 'integer',
        'uses_per_customer' => 'integer',
        'times_used' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'is_visible' => 'boolean',
        'applicable_categories' => 'array',
        'excluded_items' => 'array',
        'free_item_quantity' => 'integer',
    ];

    public function freeItem()
    {
        return $this->belongsTo(MenuItem::class, 'free_item_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'promo_code', 'code');
    }

    public function usage()
    {
        return $this->orders();
    }

    // Compatibility accessors
    public function getCodeAttribute(): ?string
    {
        return $this->promo_code;
    }

    public function setCodeAttribute($value): void
    {
        $this->attributes['promo_code'] = $value;
    }

    public function getDiscountTypeAttribute(): ?string
    {
        return $this->type;
    }

    public function setDiscountTypeAttribute($value): void
    {
        $this->attributes['type'] = $value;
    }

    public function getValidFromAttribute()
    {
        return $this->start_date;
    }

    public function setValidFromAttribute($value): void
    {
        $this->attributes['start_date'] = $value;
    }

    public function getValidToAttribute()
    {
        return $this->end_date;
    }

    public function setValidToAttribute($value): void
    {
        $this->attributes['end_date'] = $value;
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->start_date && \Carbon\Carbon::parse($this->start_date)->isFuture()) {
            return false;
        }

        if ($this->end_date && \Carbon\Carbon::parse($this->end_date)->endOfDay()->isPast()) {
            return false;
        }

        if ($this->maximum_uses !== null && $this->times_used >= $this->maximum_uses) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $orderAmount): float
    {
        if (!$this->isValid()) {
            return 0.0;
        }

        if ($this->minimum_order_amount !== null && $orderAmount < (float) $this->minimum_order_amount) {
            return 0.0;
        }

        $discount = 0.0;

        if ($this->type === 'percentage') {
            $discount = $orderAmount * ((float) $this->discount_value / 100);
        } elseif ($this->type === 'fixed') {
            $discount = (float) $this->discount_value;
        }

        return min($discount, $orderAmount);
    }
}

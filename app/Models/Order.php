<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Status Constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_READY = 'ready';
    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_SERVED = 'served';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // Order Types
    public const TYPE_DINE_IN = 'dine_in';
    public const TYPE_TAKEAWAY = 'takeaway';
    public const TYPE_DELIVERY = 'delivery';

    // Payment Methods
    public const PAYMENT_CASH = 'cash';
    public const PAYMENT_CARD = 'card';
    public const PAYMENT_COD = 'cash_on_delivery';
    public const PAYMENT_ONLINE = 'online';

    protected $fillable = [
        'order_number',
        'user_id',
        'order_type',
        'table_number',
        'dining_session_id',
        'status',
        'payment_status',
        'payment_method',
        'subtotal',
        'tax_amount',
        'delivery_charge',
        'discount_amount',
        'total_amount',
        'total',
        'promo_code',
        'delivery_address',
        'customer_name',
        'customer_phone',
        'customer_email',
        'special_instructions',
        'estimated_preparation_time',
        'estimated_delivery_time',
        'preparation_started_at',
        'ready_at',
        'served_at',
        'completed_at',
        'assigned_staff_id',
        'cancellation_reason',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'estimated_delivery_time' => 'datetime',
        'preparation_started_at' => 'datetime',
        'ready_at' => 'datetime',
        'served_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_preparation_time' => 'integer',
        'table_number' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function diningSession()
    {
        return $this->belongsTo(DiningSession::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promo_code', 'code');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function assignedStaff()
    {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
    }

    // Compatibility: `tax`, `discount`, `total` helpers used in existing blades/controllers
    public function getTaxAttribute()
    {
        return $this->tax_amount;
    }

    public function setTaxAttribute($value): void
    {
        $this->attributes['tax_amount'] = $value;
    }

    public function getDiscountAttribute()
    {
        return $this->discount_amount;
    }

    public function setDiscountAttribute($value): void
    {
        $this->attributes['discount_amount'] = $value;
    }

    public function setTotalAttribute($value): void
    {
        $this->attributes['total'] = $value;
        $this->attributes['total_amount'] = $value;
    }

    public function setTotalAmountAttribute($value): void
    {
        $this->attributes['total_amount'] = $value;
        $this->attributes['total'] = $value;
    }

    public function getTotalAttribute($value)
    {
        return $value ?? ($this->attributes['total_amount'] ?? null);
    }

    public function getTotalAmountAttribute($value)
    {
        return $value ?? ($this->attributes['total'] ?? null);
    }

    public function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $idPart = $this->id ? str_pad((string) $this->id, 4, '0', STR_PAD_LEFT) : '0000';
        return "ORD-{$date}-{$idPart}";
    }

    public static function generateUniqueOrderNumber(): string
    {
        $date = now()->format('Ymd');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $suffix = strtoupper(Str::random(6));
            $orderNumber = "ORD-{$date}-{$suffix}";

            if (!self::withTrashed()->where('order_number', $orderNumber)->exists()) {
                return $orderNumber;
            }
        }

        // Fallback to a longer unique suffix if collisions persist.
        $fallback = strtoupper(Str::random(12));
        return "ORD-{$date}-{$fallback}";
    }

    public function getStatusColor(string $status): string
    {
        return match ($status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_CONFIRMED => 'blue',
            self::STATUS_PREPARING => 'orange',
            self::STATUS_READY => 'purple',
            self::STATUS_OUT_FOR_DELIVERY => 'indigo',
            self::STATUS_DELIVERED => 'green',
            self::STATUS_SERVED => 'teal',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_CANCELLED => 'red',
            default => 'gray',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return $this->getStatusColor((string) $this->status);
    }

    public function getStatusTextAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    public function getOrderTypeTextAttribute(): string
    {
        return match ((string) $this->order_type) {
            self::TYPE_DINE_IN => 'Dine In',
            self::TYPE_TAKEAWAY => 'Takeaway',
            self::TYPE_DELIVERY => 'Delivery',
            default => (string) $this->order_type,
        };
    }

    public function getFormattedTotalAttribute(): string
    {
        $symbol = config('restaurant.payment.currency_symbol', '₹');
        return $symbol . number_format((float) $this->total, 2);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_PREPARING,
        ], true);
    }

    public function calculateTotals(): array
    {
        $subtotal = (float) $this->items->sum(function (OrderItem $item) {
            return $item->getTotal();
        });

        $taxRate = (float) config('restaurant.order.tax_rate', 0);
        $taxAmount = $taxRate > 0 ? ($subtotal * ($taxRate / 100)) : 0;
        $total = $subtotal + $taxAmount + (float) $this->delivery_charge - (float) $this->discount_amount;

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
        ];
    }

    /**
     * Get order progress percentage for order tracking
     */
    public function getProgressPercentage(): int
    {
        $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'completed', 'delivered'];
        $currentIndex = array_search($this->status, $statuses);
        $totalStatuses = count($statuses) - 1;

        return $totalStatuses > 0 ? (int) round(($currentIndex / $totalStatuses) * 100) : 0;
    }

    /**
     * Get status index for order tracking
     */
    public function getStatusIndex(string $status): int
    {
        $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'completed', 'delivered'];
        return array_search($status, $statuses) ?? 0;
    }
}

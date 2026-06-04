<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DiningSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_code',
        'user_id',
        'table_number',
        'number_of_people',
        'status',
        'start_time',
        'end_time',
        'total_bill',
        'amount_paid',
        'remaining_balance',
        'payment_completed',
        'notes',
        'assigned_waiter',
        'custom_meal_preferences',
        'exit_password',
        'exit_with_admin_password',
        'last_order_time',
    ];

    protected $casts = [
        'table_number' => 'integer',
        'number_of_people' => 'integer',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_bill' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'payment_completed' => 'boolean',
        'custom_meal_preferences' => 'array',
        'exit_with_admin_password' => 'boolean',
        'last_order_time' => 'datetime',
    ];

    // Status Constants (must match the DB enum)
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_PAYMENT_PENDING = 'payment_pending';
    public const STATUS_CANCELLED = 'cancelled';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_number', 'table_number');
    }

    public function waiter()
    {
        return $this->belongsTo(Staff::class, 'assigned_waiter');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    // Compatibility helpers used in some existing Blade templates
    public function getGuestCountAttribute(): int
    {
        return (int) $this->number_of_people;
    }

    public function setGuestCountAttribute($value): void
    {
        $this->attributes['number_of_people'] = $value;
    }

    public static function generateSessionCode(): string
    {
        return 'DS-' . strtoupper(Str::random(8));
    }
}


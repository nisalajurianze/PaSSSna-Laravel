<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_number',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'reservation_date',
        'reservation_time',
        'start_at',
        'end_at',
        'duration_minutes',
        'number_of_people',
        'table_numbers',
        'table_count',
        'status',
        'reservation_type',
        'special_requests',
        'occasion_notes',
        'confirmed_by',
        'confirmed_at',
        'confirmation_message',
        'arrival_time',
        'seated_time',
        'departure_time',
        'deposit_amount',
        'deposit_paid',
        'cancellation_reason',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'reservation_time' => 'string',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'arrival_time' => 'datetime',
        'seated_time' => 'datetime',
        'departure_time' => 'datetime',
        'duration_minutes' => 'integer',
        'number_of_people' => 'integer',
        'table_numbers' => 'array',
        'table_count' => 'integer',
        'deposit_amount' => 'decimal:2',
        'deposit_paid' => 'boolean',
    ];

    // Status Constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_SEATED = 'seated';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_NO_SHOW = 'no_show';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function tables()
    {
        return $this->belongsToMany(Table::class, 'reservation_table')
            ->withTimestamps();
    }

    // Compatibility accessors (keep older Blade templates working during refactor)
    public function getNameAttribute()
    {
        return $this->customer_name;
    }

    public function getEmailAttribute()
    {
        return $this->customer_email;
    }

    public function getPhoneAttribute()
    {
        return $this->customer_phone;
    }

    public function getGuestsAttribute()
    {
        return $this->number_of_people;
    }

    // Helpers
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_CONFIRMED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_SEATED => 'blue',
            self::STATUS_COMPLETED => 'gray',
            self::STATUS_CANCELLED => 'red',
            self::STATUS_NO_SHOW => 'red',
            default => 'gray',
        };
    }

    public function getStatusTextAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function canBeCancelled(): bool
    {
        if (!in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED], true)) {
            return false;
        }

        $start = $this->start_at ?: $this->buildStartAt();
        return $start ? now()->lessThan($start) : false;
    }

    public function updateStatus(string $status, ?string $message = null): void
    {
        $data = ['status' => $status];

        if ($status === self::STATUS_CONFIRMED) {
            $data['confirmed_by'] = Auth::id();
            $data['confirmed_at'] = now();
            if ($message !== null) {
                $data['confirmation_message'] = $message;
            }
        }

        if ($status === self::STATUS_REJECTED || $status === self::STATUS_CANCELLED) {
            if ($message !== null) {
                $data['cancellation_reason'] = $message;
            }
        }

        $this->fill($data);
        $this->save();
    }

    public function buildStartAt(): ?\Carbon\Carbon
    {
        if (!$this->reservation_date || !$this->reservation_time) {
            return null;
        }

        return \Carbon\Carbon::parse($this->reservation_date . ' ' . $this->reservation_time);
    }
}

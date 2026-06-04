<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftSchedule extends Model
{
    use HasFactory;

    // Status Constants
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';

    // Shift Type Constants
    public const TYPE_REGULAR = 'regular';
    public const TYPE_EXTENDED = 'extended';
    public const TYPE_SPLIT = 'split';

    protected $fillable = [
        'staff_id',
        'shift_date',
        'start_time',
        'end_time',
        'shift_type',
        'status',
        'location',
        'assigned_tables',
        'assigned_duties',
        'notes',
        'hours_worked',
        'clock_in_time',
        'clock_out_time',
        'overtime_hours',
        'is_approved',
        'approved_by',
        'created_by',
    ];

    protected $casts = [
        'shift_date' => 'date',
        'assigned_tables' => 'array',
        'assigned_duties' => 'array',
        'hours_worked' => 'decimal:2',
        'clock_in_time' => 'datetime',
        'clock_out_time' => 'datetime',
        'overtime_hours' => 'decimal:2',
        'is_approved' => 'boolean',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getDayOfWeekAttribute(): ?string
    {
        return $this->shift_date ? \Carbon\Carbon::parse($this->shift_date)->format('l') : null;
    }

    public function getStartTimeAttribute($value): ?string
    {
        return $value ? \Carbon\Carbon::parse($value)->format('H:i') : null;
    }

    public function getEndTimeAttribute($value): ?string
    {
        return $value ? \Carbon\Carbon::parse($value)->format('H:i') : null;
    }
}


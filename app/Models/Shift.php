<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'date',
        'shift_type',
        'start_time',
        'end_time',
        'status',
        'notes',
        'scheduled_by'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    // Status constants
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    // Accessors
    public function getDurationAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return $this->start_time->diffInHours($this->end_time);
        }
        return 0;
    }

    public function getFormattedDateAttribute()
    {
        return $this->date ? $this->date->format('M d, Y') : null;
    }

    public function getFormattedTimeAttribute()
    {
        if ($this->start_time && $this->end_time) {
            return $this->start_time->format('h:i A') . ' - ' . $this->end_time->format('h:i A');
        }
        return null;
    }
}

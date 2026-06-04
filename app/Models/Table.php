<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_number',
        'name',
        'type',
        'capacity',
        'min_capacity',
        'location',
        'area',
        'status',
        'is_active',
        'features',
        'reservation_fee',
        'sort_order',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'min_capacity' => 'integer',
        'is_active' => 'boolean',
        'reservation_fee' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    // Status Constants
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_OCCUPIED = 'occupied';
    public const STATUS_CLEANING = 'cleaning';
    public const STATUS_MAINTENANCE = 'maintenance';

    // Table Types (match migration enum)
    public const TYPE_INDOOR = 'indoor';
    public const TYPE_OUTDOOR = 'outdoor';
    public const TYPE_PRIVATE_ROOM = 'private_room';
    public const TYPE_BAR = 'bar';

    // Relationships
    public function reservations()
    {
        return $this->belongsToMany(Reservation::class, 'reservation_table')
            ->withTimestamps();
    }

    public function diningSessions()
    {
        return $this->hasMany(DiningSession::class, 'table_number', 'table_number');
    }

    // Compatibility accessor (older Blade uses $table->number)
    public function getNumberAttribute()
    {
        return $this->table_number;
    }

    public function scopeAvailable($query)
    {
        return $query
            ->where('status', self::STATUS_AVAILABLE)
            ->where('is_active', true);
    }

    public function getFullNameAttribute()
    {
        return $this->name ?: 'Table ' . $this->table_number;
    }
}


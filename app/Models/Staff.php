<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Staff extends Model
{
    use HasFactory;

    // Roles (as per migration enum)
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_CHEF = 'chef';
    public const ROLE_WAITER = 'waiter';
    public const ROLE_BARTENDER = 'bartender';
    public const ROLE_HOST = 'host';
    public const ROLE_CASHIER = 'cashier';
    public const ROLE_DELIVERY_BOY = 'delivery_boy';

    // Status (as per migration enum)
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_ON_LEAVE = 'on_leave';
    public const STATUS_TERMINATED = 'terminated';

    protected $table = 'staff';

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'photo',
        'role',
        'status',
        'salary',
        'hire_date',
        'termination_date',
        'address',
        'emergency_contact',
        'emergency_phone',
        'notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'termination_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function schedules()
    {
        return $this->hasMany(ShiftSchedule::class);
    }

    public function shifts()
    {
        return $this->schedules();
    }

    public function shiftSchedules()
    {
        return $this->schedules();
    }

    public function assignedOrders()
    {
        return $this->hasMany(Order::class, 'assigned_staff_id');
    }

    public function orders()
    {
        return $this->assignedOrders();
    }

    public function performanceReviews()
    {
        return $this->hasMany(PerformanceReview::class);
    }

    public function getNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function getIsOnLeaveAttribute(): bool
    {
        return $this->status === self::STATUS_ON_LEAVE;
    }

    public function isChef(): bool
    {
        return $this->role === self::ROLE_CHEF;
    }

    public function isWaiter(): bool
    {
        return $this->role === self::ROLE_WAITER;
    }

    public function setPasswordAttribute($value): void
    {
        if (!$value) {
            return;
        }

        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }
}

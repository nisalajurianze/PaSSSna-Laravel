<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_number',
        'report_type',
        'title',
        'start_date',
        'end_date',
        'generated_by',
        'data',
        'summary',
        'notes',
        'file_path',
        'is_archived',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'data' => 'array',
        'summary' => 'array',
        'is_archived' => 'boolean',
    ];

    // Report Types (must match DB enum)
    public const TYPE_DAILY = 'daily';
    public const TYPE_WEEKLY = 'weekly';
    public const TYPE_MONTHLY = 'monthly';
    public const TYPE_QUARTERLY = 'quarterly';
    public const TYPE_YEARLY = 'yearly';
    public const TYPE_CUSTOM = 'custom';

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public static function generateReportNumber(string $prefix = 'RPT'): string
    {
        return $prefix . '-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}


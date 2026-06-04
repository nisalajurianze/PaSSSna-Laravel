<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'reviewer_id',
        'review_date',
        'punctuality_score',
        'efficiency_score',
        'customer_feedback_score',
        'teamwork_score',
        'comments'
    ];

    protected $casts = [
        'review_date' => 'date',
        'punctuality_score' => 'integer',
        'efficiency_score' => 'integer',
        'customer_feedback_score' => 'integer',
        'teamwork_score' => 'integer'
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // Accessors
    public function getAverageScoreAttribute()
    {
        return round(
            ($this->punctuality_score + $this->efficiency_score +
             $this->customer_feedback_score + $this->teamwork_score) / 4,
            2
        );
    }

    public function getScoreGradeAttribute()
    {
        $avg = $this->average_score;
        if ($avg >= 90) return 'A';
        if ($avg >= 80) return 'B';
        if ($avg >= 70) return 'C';
        if ($avg >= 60) return 'D';
        return 'F';
    }
}

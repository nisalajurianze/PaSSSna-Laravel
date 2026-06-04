<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'menu_item_id',
        'order_id',
        'rating',
        'title',
        'comment',
        'status',
        'is_featured',
        'helpful_count',
        'reply',
        'replied_by',
        'replied_at',
        'verified_purchase'
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_featured' => 'boolean',
        'helpful_count' => 'integer',
        'verified_purchase' => 'boolean',
        'replied_at' => 'datetime'
    ];

    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function replier()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
                    ->where('status', self::STATUS_APPROVED);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating)
                    ->where('status', self::STATUS_APPROVED);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->where('status', self::STATUS_APPROVED)
                    ->orderBy('created_at', 'desc')
                    ->limit($limit);
    }

    // Methods
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute()
    {
        return ucfirst($this->status);
    }

    public function getStarRatingAttribute()
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '★';
            } else {
                $stars .= '☆';
            }
        }
        return $stars;
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('F j, Y');
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getInitialsAttribute()
    {
        return $this->user ? $this->user->initials : 'GU';
    }

    public function getUserNameAttribute()
    {
        return $this->user ? $this->user->name : 'Anonymous';
    }

    public function markHelpful()
    {
        $this->increment('helpful_count');
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function hasReply()
    {
        return !empty($this->reply);
    }

    public function getReplyTimeAgoAttribute()
    {
        return $this->replied_at ? $this->replied_at->diffForHumans() : null;
    }
}

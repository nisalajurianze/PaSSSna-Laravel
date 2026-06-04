<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class MenuItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'base_price',
        'sizes',
        'flavors',
        'flavor_photos',
        'extra_toppings',
        'category',
        'food_type',
        'preparation_time',
        'is_available',
        'is_fast_moving',
        'is_recommended',
        'is_customizable',
        'ingredients',
        'allergens',
        'nutrition_info',
        'offer_price',
        'offer_valid_from',
        'offer_valid_to',
        'offer_valid_until',
        'min_order_qty',
        'max_order_qty',
        'image',
        'sort_order',
        'total_orders',
        'average_rating',
        'rating_count',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'sizes' => 'array',
        'flavors' => 'array',
        'flavor_photos' => 'array',
        'extra_toppings' => 'array',
        'ingredients' => 'array',
        'allergens' => 'array',
        'nutrition_info' => 'array',
        'size_flavor_prices' => 'array',
        'is_available' => 'boolean',
        'is_fast_moving' => 'boolean',
        'is_recommended' => 'boolean',
        'is_customizable' => 'boolean',
        'offer_valid_from' => 'date',
        'offer_valid_to' => 'date',
        'offer_valid_until' => 'date',
        'total_orders' => 'integer',
        'average_rating' => 'decimal:2',
        'rating_count' => 'integer',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category', 'slug');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Compatibility: many templates/controllers reference `price`
    public function getPriceAttribute()
    {
        return $this->base_price;
    }

    public function setPriceAttribute($value): void
    {
        $this->attributes['base_price'] = $value;
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeFastMoving($query)
    {
        return $query->where('is_fast_moving', true);
    }

    public function scopeWithOffer($query)
    {
        return $query->whereNotNull('offer_price')
            ->where(function ($q) {
                $q->whereNull('offer_valid_until')->orWhere('offer_valid_until', '>=', now()->toDateString());
            });
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function isOfferActive(): bool
    {
        if (!$this->offer_price) {
            return false;
        }

        $until = $this->offer_valid_until ?? $this->offer_valid_to;
        if ($until) {
            return Carbon::parse($until)->endOfDay()->gte(now());
        }

        return true;
    }

    public function getCurrentPriceAttribute()
    {
        return $this->isOfferActive() ? ($this->offer_price ?? $this->price) : $this->price;
    }

    public function getDiscountAmount(): float
    {
        if (!$this->isOfferActive() || !$this->offer_price) {
            return 0.0;
        }

        return max(0.0, (float) $this->price - (float) $this->offer_price);
    }

    public function getDiscountPercentage(): int
    {
        if (!$this->isOfferActive() || !$this->offer_price || (float) $this->price <= 0) {
            return 0;
        }

        return (int) round((($this->price - $this->offer_price) / $this->price) * 100);
    }

    public function toggleAvailability(): void
    {
        $this->is_available = !$this->is_available;
        $this->save();
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            if (Str::startsWith($this->image, ['http://', 'https://', 'data:', '/'])) {
                return $this->image;
            }
            $publicPath = public_path('storage/' . ltrim($this->image, '/'));
            if (file_exists($publicPath)) {
                return asset('storage/' . ltrim($this->image, '/'));
            }
        }

        $category = $this->category ?: 'default';
        $placeholder = "images/menu/placeholders/{$category}.svg";
        if (!file_exists(public_path($placeholder))) {
            $placeholder = 'images/menu/placeholders/default.svg';
        }

        return asset($placeholder);
    }

    /**
     * Get price for specific size and flavor combination
     */
    public function getPriceForSizeAndFlavor($size = 'regular', $flavor = null)
    {
        $basePrice = (float) $this->base_price;

        // Get size price modifier
        if (is_array($this->sizes) && !empty($this->sizes)) {
            foreach ($this->sizes as $sizeData) {
                if (is_array($sizeData) && isset($sizeData['name']) &&
                    strtolower($sizeData['name']) === strtolower($size)) {
                    $basePrice += (float) ($sizeData['price_modifier'] ?? 0);
                    break;
                }
            }
        }

        // Add flavor price
        if ($flavor && is_array($this->flavors) && !empty($this->flavors)) {
            foreach ($this->flavors as $flavorData) {
                if (is_array($flavorData) && isset($flavorData['name']) &&
                    strtolower($flavorData['name']) === strtolower($flavor)) {
                    $basePrice += (float) ($flavorData['price'] ?? 0);
                    break;
                }
            }
        }

        // Check for offer price (only applies to regular size)
        if ($this->isOfferActive() && strtolower($size) === 'regular') {
            $basePrice = min((float) $this->offer_price, $basePrice);
        }

        return $basePrice;
    }
}

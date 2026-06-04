<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'item_name',
        'item_code',
        'category',
        'unit',
        'current_quantity',
        'current_stock',
        'minimum_quantity',
        'min_stock',
        'maximum_quantity',
        'unit_cost',
        'total_value',
        'status',
        'supplier_name',
        'supplier_contact',
        'last_restocked_date',
        'expiry_date',
        'storage_location',
        'notes',
        'is_active',
        'reorder_quantity',
        'daily_usage_rate',
        'days_of_supply'
    ];

    protected $casts = [
        'current_quantity' => 'decimal:3',
        'minimum_quantity' => 'decimal:3',
        'maximum_quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'total_value' => 'decimal:2',
        'reorder_quantity' => 'decimal:3',
        'daily_usage_rate' => 'decimal:3',
        'last_restocked_date' => 'date',
        'expiry_date' => 'date'
    ];

    // Status Constants
    const STATUS_IN_STOCK = 'in_stock';
    const STATUS_LOW_STOCK = 'low_stock';
    const STATUS_OUT_OF_STOCK = 'out_of_stock';
    const STATUS_EXPIRED = 'expired';

    // Categories
    const CATEGORY_VEGETABLE = 'vegetable';
    const CATEGORY_MEAT = 'meat';
    const CATEGORY_DAIRY = 'dairy';
    const CATEGORY_SPICE = 'spice';
    const CATEGORY_GRAIN = 'grain';
    const CATEGORY_BEVERAGE = 'beverage';
    const CATEGORY_OTHER = 'other';

    // Relationships
    public function ingredientUsages()
    {
        return $this->hasMany(IngredientUsage::class);
    }

    // Scopes
    public function scopeLowStock($query)
    {
        return $query->where('current_quantity', '<=', DB::raw('minimum_quantity'))
                    ->where('status', '!=', self::STATUS_OUT_OF_STOCK);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_quantity', '<=', 0)
                    ->orWhere('status', self::STATUS_OUT_OF_STOCK);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now())
                    ->where('current_quantity', '>', 0);
    }

    // Methods
    public function isLowStock(): bool
    {
        return (float) $this->current_quantity <= (float) $this->minimum_quantity;
    }

    // Compatibility accessors/mutators (older tests/templates)
    public function getCurrentStockAttribute()
    {
        return $this->current_quantity;
    }

    public function setCurrentStockAttribute($value): void
    {
        $this->attributes['current_quantity'] = $value;
    }

    public function getMinStockAttribute()
    {
        return $this->minimum_quantity;
    }

    public function setMinStockAttribute($value): void
    {
        $this->attributes['minimum_quantity'] = $value;
    }

    public function getNameAttribute()
    {
        return $this->item_name;
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['item_name'] = $value;
    }

    public function getSkuAttribute()
    {
        return $this->item_code;
    }

    public function setSkuAttribute($value): void
    {
        $this->attributes['item_code'] = $value;
    }

    public function getUnitPriceAttribute()
    {
        return $this->unit_cost;
    }

    public function setUnitPriceAttribute($value): void
    {
        $this->attributes['unit_cost'] = $value;
    }

    public function getMaxStockAttribute()
    {
        return $this->maximum_quantity;
    }

    public function setMaxStockAttribute($value): void
    {
        $this->attributes['maximum_quantity'] = $value;
    }

    public function getSupplierAttribute()
    {
        return $this->supplier_name;
    }

    public function setSupplierAttribute($value): void
    {
        $this->attributes['supplier_name'] = $value;
    }

    public function updateStatus()
    {
        if ($this->current_quantity <= 0) {
            $this->status = self::STATUS_OUT_OF_STOCK;
        } elseif ($this->current_quantity <= $this->minimum_quantity) {
            $this->status = self::STATUS_LOW_STOCK;
        } else {
            $this->status = self::STATUS_IN_STOCK;
        }

        if ($this->expiry_date && $this->expiry_date < now()) {
            $this->status = self::STATUS_EXPIRED;
        }

        $this->save();
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_IN_STOCK => 'green',
            self::STATUS_LOW_STOCK => 'yellow',
            self::STATUS_OUT_OF_STOCK => 'red',
            self::STATUS_EXPIRED => 'gray',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute()
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }
}

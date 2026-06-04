<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_item_id',
        'is_custom_meal',
        'item_name',
        'name',
        'description',
        'quantity',
        'unit_price',
        'price',
        'total_price',
        'total',
        'subtotal',
        'size',
        'flavor',
        'selected_toppings',
        'toppings',
        'extra_toppings',
        'custom_ingredients',
        'special_instructions',
        'is_prepared',
        'prepared_at',
        'prepared_by',
    ];

    protected $casts = [
        'is_custom_meal' => 'boolean',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'selected_toppings' => 'array',
        'custom_ingredients' => 'array',
        'is_prepared' => 'boolean',
        'prepared_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function preparedBy()
    {
        return $this->belongsTo(Staff::class, 'prepared_by');
    }

    // Compatibility accessors/mutators (older Blade/templates/tests)
    public function getNameAttribute(): ?string
    {
        return $this->item_name;
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['item_name'] = $value;
    }

    public function getPriceAttribute()
    {
        return $this->unit_price;
    }

    public function setPriceAttribute($value): void
    {
        $this->attributes['unit_price'] = $value;
    }

    public function getTotalAttribute()
    {
        return $this->total_price;
    }

    public function setTotalAttribute($value): void
    {
        $this->attributes['total_price'] = $value;
    }

    public function getSubtotalAttribute()
    {
        return $this->total_price;
    }

    public function setSubtotalAttribute($value): void
    {
        $this->attributes['total_price'] = $value;
    }

    public function getToppingsAttribute(): array
    {
        return $this->selected_toppings ?? [];
    }

    public function setToppingsAttribute($value): void
    {
        $this->attributes['selected_toppings'] = $value;
    }

    public function getExtraToppingsAttribute(): array
    {
        return $this->selected_toppings ?? [];
    }

    public function setExtraToppingsAttribute($value): void
    {
        $this->attributes['selected_toppings'] = $value;
    }

    public function getFormattedPriceAttribute()
    {
        $symbol = config('restaurant.payment.currency_symbol', '₹');
        return $symbol . number_format((float) $this->price, 2);
    }

    public function getFormattedSubtotalAttribute()
    {
        $symbol = config('restaurant.payment.currency_symbol', '₹');
        return $symbol . number_format((float) $this->subtotal, 2);
    }

    public function getSizeTextAttribute()
    {
        return $this->size ? ucfirst($this->size) : 'Regular';
    }

    public function getToppingsTextAttribute()
    {
        if (!$this->selected_toppings) {
            return '';
        }

        return collect($this->selected_toppings)->map(function ($topping) {
            return ucfirst($topping);
        })->implode(', ');
    }

    public function getCustomIngredientsTextAttribute()
    {
        if (!$this->custom_ingredients) {
            return '';
        }

        return collect($this->custom_ingredients)->map(function ($ingredient) {
            return $ingredient['name'] . ' (' . $ingredient['quantity'] . ')';
        })->implode(', ');
    }

    public function getTotal(): float
    {
        if ($this->total_price !== null) {
            return (float) $this->total_price;
        }

        return (float) $this->unit_price * (int) $this->quantity;
    }
}

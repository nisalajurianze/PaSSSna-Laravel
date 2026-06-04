<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'type',
        'price',
        'unit',
        'calories',
        'is_available',
        'is_vegetarian',
        'is_vegan',
        'is_gluten_free',
        'allergens',
        'image',
        'sort_order',
        'max_quantity'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_vegetarian' => 'boolean',
        'is_vegan' => 'boolean',
        'is_gluten_free' => 'boolean',
        'calories' => 'integer',
        'max_quantity' => 'integer',
        'allergens' => 'array'
    ];

    // Categories
    const CATEGORY_PROTEIN = 'protein';
    const CATEGORY_VEGETABLES = 'vegetables';
    const CATEGORY_CARBS = 'carbs';
    const CATEGORY_SAUCES = 'sauces';
    const CATEGORY_CHEESE = 'cheese';
    const CATEGORY_TOPPINGS = 'toppings';
    const CATEGORY_ADDONS = 'addons';

    // Types
    const TYPE_MEAT = 'meat';
    const TYPE_SEAFOOD = 'seafood';
    const TYPE_VEGETABLE = 'vegetable';
    const TYPE_CHEESE = 'cheese';
    const TYPE_SAUCE = 'sauce';
    const TYPE_SPICE = 'spice';
    const TYPE_OTHER = 'other';

    // Units
    const UNIT_PIECE = 'piece';
    const UNIT_SCOOP = 'scoop';
    const UNIT_SLICE = 'slice';
    const UNIT_SPOON = 'spoon';
    const UNIT_GRAM = 'gram';
    const UNIT_OUNCE = 'ounce';

    // Relationships
    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'menu_item_custom_ingredients')
                    ->withPivot(['is_default', 'additional_price', 'max_quantity']);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeVegetarian($query)
    {
        return $query->where('is_vegetarian', true);
    }

    public function scopeVegan($query)
    {
        return $query->where('is_vegan', true);
    }

    public function scopeGlutenFree($query)
    {
        return $query->where('is_gluten_free', true);
    }

    // Methods
    public function getCategoryTextAttribute()
    {
        return ucfirst($this->category);
    }

    public function getTypeTextAttribute()
    {
        return ucfirst($this->type);
    }

    public function getUnitTextAttribute()
    {
        return match($this->unit) {
            self::UNIT_PIECE => 'piece',
            self::UNIT_SCOOP => 'scoop',
            self::UNIT_SLICE => 'slice',
            self::UNIT_SPOON => 'spoon',
            self::UNIT_GRAM => 'gram',
            self::UNIT_OUNCE => 'ounce',
            default => ucfirst($this->unit)
        };
    }

    public function getFormattedPriceAttribute()
    {
        if ($this->price === null || $this->price == 0) {
            return $this->price === null ? 'N/A' : 'Free';
        }
        return '₹' . number_format((float) $this->price, 2);
    }

    public function getAllergensListAttribute()
    {
        if (!$this->allergens || empty($this->allergens)) {
            return 'None';
        }
        return implode(', ', array_map('ucfirst', $this->allergens));
    }

    public function isProtein()
    {
        return $this->category === self::CATEGORY_PROTEIN;
    }

    public function isVegetable()
    {
        return $this->category === self::CATEGORY_VEGETABLES;
    }

    public function isSauce()
    {
        return $this->category === self::CATEGORY_SAUCES;
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/ingredients/' . $this->image);
        }
        return asset('images/default-ingredient.jpg');
    }

    public function getMaxQuantityForMenuItem($menuItemId)
    {
        $pivot = $this->menuItems()->where('menu_item_id', $menuItemId)->first();
        return $pivot ? $pivot->pivot->max_quantity : $this->max_quantity;
    }

    public function getAdditionalPriceForMenuItem($menuItemId)
    {
        $pivot = $this->menuItems()->where('menu_item_id', $menuItemId)->first();
        return $pivot ? $pivot->pivot->additional_price : $this->price;
    }

    public function isDefaultForMenuItem($menuItemId)
    {
        $pivot = $this->menuItems()->where('menu_item_id', $menuItemId)->first();
        return $pivot ? $pivot->pivot->is_default : false;
    }
}

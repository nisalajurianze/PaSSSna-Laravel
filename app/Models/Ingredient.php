<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'unit',
        'is_allergen',
        'is_vegetarian',
        'is_vegan',
        'is_gluten_free',
        'calories_per_unit',
        'sort_order'
    ];

    protected $casts = [
        'is_allergen' => 'boolean',
        'is_vegetarian' => 'boolean',
        'is_vegan' => 'boolean',
        'is_gluten_free' => 'boolean',
        'calories_per_unit' => 'decimal:2',
        'sort_order' => 'integer'
    ];

    /**
     * Scope to only include allergens
     */
    public function scopeAllergens($query)
    {
        return $query->where('is_allergen', true);
    }

    /**
     * Scope to only include vegetarian ingredients
     */
    public function scopeVegetarian($query)
    {
        return $query->where('is_vegetarian', true);
    }

    /**
     * Scope to only include vegan ingredients
     */
    public function scopeVegan($query)
    {
        return $query->where('is_vegan', true);
    }

    /**
     * Scope to only include gluten-free ingredients
     */
    public function scopeGlutenFree($query)
    {
        return $query->where('is_gluten_free', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}

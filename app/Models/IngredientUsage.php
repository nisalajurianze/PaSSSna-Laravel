<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class IngredientUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'menu_item_id',
        'order_item_id',
        'quantity_used',
        'unit',
        'usage_date',
        'type',
        'notes',
        'recorded_by'
    ];

    protected $casts = [
        'quantity_used' => 'decimal:4',
        'usage_date' => 'date'
    ];

    // Usage Types
    const TYPE_ORDER = 'order';
    const TYPE_WASTAGE = 'wastage';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_TESTING = 'testing';
    const TYPE_COMPLIMENTARY = 'complimentary';

    // Relationships
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('usage_date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('usage_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('usage_date', now()->month)
                    ->whereYear('usage_date', now()->year);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByInventory($query, $inventoryId)
    {
        return $query->where('inventory_id', $inventoryId);
    }

    public function scopeByMenuItem($query, $menuItemId)
    {
        return $query->where('menu_item_id', $menuItemId);
    }

    // Methods
    public function getTypeTextAttribute()
    {
        return match($this->type) {
            self::TYPE_ORDER => 'Order',
            self::TYPE_WASTAGE => 'Wastage',
            self::TYPE_ADJUSTMENT => 'Adjustment',
            self::TYPE_TESTING => 'Testing',
            self::TYPE_COMPLIMENTARY => 'Complimentary',
            default => ucfirst($this->type)
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            self::TYPE_ORDER => 'green',
            self::TYPE_WASTAGE => 'red',
            self::TYPE_ADJUSTMENT => 'blue',
            self::TYPE_TESTING => 'yellow',
            self::TYPE_COMPLIMENTARY => 'purple',
            default => 'gray'
        };
    }

    public function getFormattedQuantityAttribute()
    {
        $unit = $this->unit;
        $quantity = $this->quantity_used;

        // Handle null quantity
        if ($quantity === null) {
            return 'N/A';
        }

        // Format based on unit
        switch ($unit) {
            case 'kg':
            case 'liter':
                if ((float) $quantity >= 1) {
                    return number_format((float) $quantity, 2) . ' ' . $unit;
                } else {
                    return number_format((float) $quantity * 1000, 0) . ' ' . ($unit === 'kg' ? 'g' : 'ml');
                }

            case 'gram':
            case 'ml':
                return number_format((float) $quantity, 0) . ' ' . $unit;

            default:
                return number_format((float) $quantity, 0) . ' ' . $unit;
        }
    }

    public function getCostAttribute()
    {
        if (!$this->inventory || $this->quantity_used === null) return 0;

        return (float) $this->quantity_used * (float) $this->inventory->cost_per_unit;
    }

    public function getFormattedCostAttribute()
    {
        return '₹' . number_format((float) $this->cost, 2);
    }

    public function getMenuItemNameAttribute()
    {
        return $this->menuItem ? $this->menuItem->name : 'N/A';
    }

    public function getInventoryNameAttribute()
    {
        return $this->inventory ? $this->inventory->name : 'N/A';
    }

    public function getFormattedDateAttribute()
    {
        if (!$this->usage_date) {
            return 'N/A';
        }
        return \Carbon\Carbon::parse($this->usage_date)->format('M j, Y');
    }

    public static function recordOrderUsage($orderItem, $ingredientData)
    {
        foreach ($ingredientData as $data) {
            self::create([
                'inventory_id' => $data['inventory_id'],
                'menu_item_id' => $orderItem->menu_item_id,
                'order_item_id' => $orderItem->id,
                'quantity_used' => $data['quantity'],
                'unit' => $data['unit'],
                'usage_date' => now(),
                'type' => self::TYPE_ORDER,
                'notes' => 'Order #' . $orderItem->order->order_number,
                'recorded_by' => Auth::id() ?? 0
            ]);

            // Reduce inventory stock
            $inventory = Inventory::find($data['inventory_id']);
            if ($inventory) {
                $inventory->reduceStock($data['quantity']);
            }
        }
    }

    public static function recordWastage($inventoryId, $quantity, $reason, $recordedBy)
    {
        $inventory = Inventory::find($inventoryId);
        if (!$inventory) return false;

        self::create([
            'inventory_id' => $inventoryId,
            'quantity_used' => $quantity,
            'unit' => $inventory->unit,
            'usage_date' => now(),
            'type' => self::TYPE_WASTAGE,
            'notes' => $reason,
            'recorded_by' => $recordedBy
        ]);

        $inventory->reduceStock($quantity);
        return true;
    }

    public static function getDailyUsageReport($date = null)
    {
        $date = $date ?? today();

        $usages = self::whereDate('usage_date', $date)
            ->with(['inventory', 'menuItem'])
            ->get();

        $report = [
            'date' => $date,
            'total_items_used' => $usages->count(),
            'total_cost' => $usages->sum('cost'),
            'by_type' => [],
            'top_items' => []
        ];

        // Group by type
        foreach ($usages->groupBy('type') as $type => $typeUsages) {
            $report['by_type'][$type] = [
                'count' => $typeUsages->count(),
                'cost' => $typeUsages->sum('cost')
            ];
        }

        // Top 5 inventory items used
        $inventoryUsage = $usages->groupBy('inventory_id');
        foreach ($inventoryUsage as $inventoryId => $inventoryUsages) {
            $inventory = $inventoryUsages->first()->inventory;
            $report['top_items'][] = [
                'name' => $inventory->name,
                'quantity' => $inventoryUsages->sum('quantity_used'),
                'cost' => $inventoryUsages->sum('cost')
            ];
        }

        // Sort by cost
        usort($report['top_items'], function ($a, $b) {
            return $b['cost'] <=> $a['cost'];
        });

        $report['top_items'] = array_slice($report['top_items'], 0, 5);

        return $report;
    }

    public static function getMonthlyUsageTrend($months = 6)
    {
        $endDate = now()->endOfMonth();
        $startDate = now()->subMonths($months - 1)->startOfMonth();

        $usages = self::whereBetween('usage_date', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(usage_date, "%Y-%m") as month,
                        SUM(quantity_used) as total_quantity,
                        SUM(quantity_used * (SELECT cost_per_unit FROM inventory WHERE id = ingredient_usages.inventory_id)) as total_cost')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return $usages;
    }
}

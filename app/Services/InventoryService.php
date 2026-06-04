<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\IngredientUsage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryService
{
    public function updateStock($inventoryId, $quantity, $type = 'add', $notes = null)
    {
        $inventory = Inventory::findOrFail($inventoryId);

        DB::transaction(function () use ($inventory, $quantity, $type, $notes) {
            $oldStock = $inventory->current_stock;

            if ($type === 'add') {
                $inventory->current_stock += $quantity;
                $usageType = IngredientUsage::TYPE_ADJUSTMENT;
            } elseif ($type === 'subtract') {
                if ($inventory->current_stock < $quantity) {
                    throw new \Exception('Insufficient stock available.');
                }
                $inventory->current_stock -= $quantity;
                $usageType = IngredientUsage::TYPE_ADJUSTMENT;
            } elseif ($type === 'wastage') {
                if ($inventory->current_stock < $quantity) {
                    throw new \Exception('Insufficient stock available.');
                }
                $inventory->current_stock -= $quantity;
                $usageType = IngredientUsage::TYPE_WASTAGE;
            }

            $inventory->updateStatus();
            $inventory->save();

            // Record the transaction
            IngredientUsage::create([
                'inventory_id' => $inventory->id,
                'quantity_used' => $quantity,
                'unit' => $inventory->unit,
                'usage_date' => now(),
                'type' => $usageType,
                'notes' => $notes ?? "Stock $type: $oldStock -> {$inventory->current_stock}",
                'recorded_by' => Auth::id(),
            ]);
        });

        return $inventory;
    }

    public function restockInventory($inventoryId, $quantity, $cost = null, $supplierInfo = null)
    {
        $inventory = Inventory::findOrFail($inventoryId);

        DB::transaction(function () use ($inventory, $quantity, $cost, $supplierInfo) {
            $oldStock = $inventory->current_stock;

            $inventory->current_stock += $quantity;

            if ($cost) {
                $inventory->cost_per_unit = $cost;
            }

            if ($supplierInfo) {
                $inventory->supplier_name = $supplierInfo['name'] ?? $inventory->supplier_name;
                $inventory->supplier_contact = $supplierInfo['contact'] ?? $inventory->supplier_contact;
            }

            $inventory->last_restocked = now();
            $inventory->updateStatus();
            $inventory->save();

            // Record the restocking
            IngredientUsage::create([
                'inventory_id' => $inventory->id,
                'quantity_used' => $quantity,
                'unit' => $inventory->unit,
                'usage_date' => now(),
                'type' => 'restock',
                'notes' => "Restocked: $quantity {$inventory->unit}. Old stock: $oldStock, New stock: {$inventory->current_stock}",
                'recorded_by' => Auth::id(),
            ]);
        });

        return $inventory;
    }

    public function checkLowStockItems()
    {
        return Inventory::whereColumn('current_quantity', '<=', 'minimum_quantity')
            ->where('status', '!=', Inventory::STATUS_OUT_OF_STOCK)
            ->get();
    }

    public function getLowStockItems()
    {
        return Inventory::where('is_active', true)
            ->whereColumn('current_quantity', '<=', 'minimum_quantity')
            ->get();
    }

    public function getStockValue()
    {
        $inventory = Inventory::all();

        $totalValue = 0;
        $totalItems = 0;
        $lowStockValue = 0;
        $outOfStockValue = 0;

        foreach ($inventory as $item) {
            $itemValue = $item->current_stock * $item->cost_per_unit;
            $totalValue += $itemValue;
            $totalItems++;

            if ($item->isLowStock()) {
                $lowStockValue += $itemValue;
            }

            if ($item->status === Inventory::STATUS_OUT_OF_STOCK) {
                $outOfStockValue += $item->max_stock * $item->cost_per_unit; // Potential value if fully stocked
            }
        }

        return [
            'total_value' => $totalValue,
            'total_items' => $totalItems,
            'average_item_value' => $totalItems > 0 ? $totalValue / $totalItems : 0,
            'low_stock_value' => $lowStockValue,
            'out_of_stock_potential' => $outOfStockValue,
        ];
    }

    public function getInventoryTurnover($period = 'month')
    {
        $startDate = null;
        $endDate = now();

        switch ($period) {
            case 'week':
                $startDate = now()->subWeek();
                break;

            case 'month':
                $startDate = now()->subMonth();
                break;

            case 'quarter':
                $startDate = now()->subMonths(3);
                break;

            case 'year':
                $startDate = now()->subYear();
                break;
        }

        // Get total usage cost
        $usageCost = IngredientUsage::whereBetween('usage_date', [$startDate, $endDate])
            ->where('type', '!=', 'restock')
            ->sum(DB::raw('quantity_used * (SELECT cost_per_unit FROM inventory WHERE id = ingredient_usages.inventory_id)'));

        // Get average inventory value
        $avgInventoryValue = $this->getAverageInventoryValue($startDate, $endDate);

        // Calculate turnover ratio
        $turnoverRatio = $avgInventoryValue > 0 ? $usageCost / $avgInventoryValue : 0;

        // Calculate days to sell inventory
        $daysInPeriod = $startDate->diffInDays($endDate);
        $daysToSell = $turnoverRatio > 0 ? $daysInPeriod / $turnoverRatio : 0;

        return [
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'usage_cost' => $usageCost,
            'average_inventory_value' => $avgInventoryValue,
            'turnover_ratio' => $turnoverRatio,
            'days_to_sell' => $daysToSell,
            'days_in_period' => $daysInPeriod,
        ];
    }

    private function getAverageInventoryValue($startDate, $endDate)
    {
        // This is a simplified calculation
        // In production, you might want to track inventory value daily

        $inventoryAtStart = Inventory::where('created_at', '<=', $startDate)->get();
        $inventoryAtEnd = Inventory::all();

        $valueAtStart = 0;
        foreach ($inventoryAtStart as $item) {
            $valueAtStart += $item->current_stock * $item->cost_per_unit;
        }

        $valueAtEnd = 0;
        foreach ($inventoryAtEnd as $item) {
            $valueAtEnd += $item->current_stock * $item->cost_per_unit;
        }

        return ($valueAtStart + $valueAtEnd) / 2;
    }

    public function getExpiringItems($days = 7)
    {
        return Inventory::whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addDays($days))
            ->where('current_stock', '>', 0)
            ->orderBy('expiry_date')
            ->get();
    }

    public function generateInventoryReport($startDate, $endDate)
    {
        $inventoryItems = Inventory::all();
        $report = [];

        foreach ($inventoryItems as $item) {
            // Get usage during period
            $usage = IngredientUsage::where('inventory_id', $item->id)
                ->whereBetween('usage_date', [$startDate, $endDate])
                ->where('type', '!=', 'restock')
                ->sum('quantity_used');

            // Get restocks during period
            $restocks = IngredientUsage::where('inventory_id', $item->id)
                ->whereBetween('usage_date', [$startDate, $endDate])
                ->where('type', 'restock')
                ->sum('quantity_used');

            $report[] = [
                'item' => $item->name,
                'category' => $item->category_text,
                'unit' => $item->unit_text,
                'starting_stock' => $item->current_stock - $restocks + $usage,
                'restocks' => $restocks,
                'usage' => $usage,
                'ending_stock' => $item->current_stock,
                'cost_per_unit' => $item->cost_per_unit,
                'total_value' => $item->current_stock * $item->cost_per_unit,
                'status' => $item->status_text,
                'reorder_level' => $item->reorder_level,
                'is_low_stock' => $item->isLowStock(),
            ];
        }

        // Sort by total value descending
        usort($report, function ($a, $b) {
            return $b['total_value'] <=> $a['total_value'];
        });

        return $report;
    }

    public function predictRestockNeeds($days = 30)
    {
        $inventoryItems = Inventory::all();
        $predictions = [];

        foreach ($inventoryItems as $item) {
            // Calculate average daily usage from last 30 days
            $avgDailyUsage = IngredientUsage::where('inventory_id', $item->id)
                ->where('usage_date', '>=', now()->subDays(30))
                ->where('type', '!=', 'restock')
                ->avg('quantity_used') ?? 0;

            $daysUntilEmpty = $avgDailyUsage > 0 ? $item->current_stock / $avgDailyUsage : 999;
            $needsRestock = $daysUntilEmpty <= $days;

            $suggestedRestock = 0;
            if ($needsRestock) {
                // Suggest enough to reach max stock
                $suggestedRestock = $item->max_stock - $item->current_stock;
                // Or suggest based on expected usage
                $expectedUsage = $avgDailyUsage * $days;
                $suggestedRestock = max($suggestedRestock, $expectedUsage - $item->current_stock);
            }

            $predictions[] = [
                'item' => $item,
                'current_stock' => $item->current_stock,
                'avg_daily_usage' => $avgDailyUsage,
                'days_until_empty' => $daysUntilEmpty,
                'needs_restock' => $needsRestock,
                'suggested_restock' => $suggestedRestock,
                'restock_urgency' => $this->calculateUrgency($daysUntilEmpty),
            ];
        }

        // Sort by urgency
        usort($predictions, function ($a, $b) {
            return $a['restock_urgency'] <=> $b['restock_urgency'];
        });

        return $predictions;
    }

    private function calculateUrgency($daysUntilEmpty)
    {
        if ($daysUntilEmpty <= 1) return 3; // High urgency
        if ($daysUntilEmpty <= 3) return 2; // Medium urgency
        if ($daysUntilEmpty <= 7) return 1; // Low urgency
        return 0; // No urgency
    }
}

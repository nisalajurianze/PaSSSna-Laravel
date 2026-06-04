<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;

class InventoryController extends Controller
{
    /**
     * Get all inventory items
     */
    public function index(Request $request)
    {
        $query = Inventory::query();

        if ($request->has('low_stock')) {
            $query->where('quantity', '<=', 'low_stock_threshold');
        }

        $items = $query->orderBy('name')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * Update inventory item
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:inventory,id',
            'quantity' => 'required|integer|min:0'
        ]);

        $item = Inventory::findOrFail($validated['id']);
        $item->update(['quantity' => $validated['quantity']]);

        return response()->json([
            'success' => true,
            'message' => 'Inventory updated',
            'data' => $item
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Inventory::query();

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('status')) {
            if ($request->status === 'low') {
                $query->whereRaw('current_quantity <= minimum_quantity');
            } elseif ($request->status === 'out') {
                $query->where('current_quantity', 0);
            }
        }

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $inventory = $query->latest()->paginate(20);

        $stats = [
            'total_items' => Inventory::count(),
            'low_stock' => Inventory::whereRaw('current_quantity <= minimum_quantity')->count(),
            'out_of_stock' => Inventory::where('current_quantity', 0)->count(),
            'total_value' => Inventory::sum('total_value'),
        ];

        return view('admin.inventory.index', compact('inventory', 'stats'));
    }

    public function lowStock()
    {
        $lowStockItems = Inventory::whereRaw('current_quantity <= minimum_quantity')
            ->orderBy('current_quantity')
            ->paginate(20);

        return view('admin.inventory.low-stock', compact('lowStockItems'));
    }

    public function create()
    {
        return view('admin.inventory.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:vegetable,meat,dairy,spice,grain,beverage,other',
            'unit' => 'required|in:kg,g,l,ml,piece,pack,dozen',
            'current_quantity' => 'required|numeric|min:0',
            'minimum_quantity' => 'required|numeric|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'supplier' => 'nullable|string',
            'expiry_date' => 'nullable|date',
        ]);

        // Map form field name to database field item_name
        $data = $request->all();
        $data['item_name'] = $data['name'];
        unset($data['name']);

        // Generate item_code if not provided
        if (!isset($data['item_code']) || empty($data['item_code'])) {
            $data['item_code'] = 'INV-' . strtoupper(substr(md5(uniqid() . $data['item_name']), 0, 8));
        }

        // Set default values for any missing fields
        $data['maximum_quantity'] = $data['maximum_quantity'] ?? $data['minimum_quantity'] ?? 100;
        $data['total_value'] = $data['total_value'] ?? 0;
        $data['reorder_quantity'] = $data['reorder_quantity'] ?? 10;

        Inventory::create($data);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item added successfully.');
    }

    public function show(Inventory $item)
    {
        return view('admin.inventory.show', compact('item'));
    }

    public function edit(Inventory $item)
    {
        return view('admin.inventory.edit', compact('item'));
    }

    public function update(Request $request, Inventory $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'unit' => 'required|string',
            'minimum_quantity' => 'required|numeric|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'supplier' => 'nullable|string',
        ]);

        $item->update($request->all());

        return back()->with('success', 'Inventory item updated successfully.');
    }

    public function updateStock(Request $request, Inventory $item)
    {
        $request->validate([
            'quantity' => 'required|numeric',
            'type' => 'required|in:add,subtract,set',
            'reason' => 'nullable|string',
        ]);

        switch ($request->type) {
            case 'add':
                $item->current_quantity += $request->quantity;
                break;
            case 'subtract':
                $item->current_quantity = max(0, $item->current_quantity - $request->quantity);
                break;
            case 'set':
                $item->current_quantity = $request->quantity;
                break;
        }

        $item->save();

        return back()->with('success', 'Stock updated successfully.');
    }

    public function restock(Request $request, Inventory $item)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1',
            'supplier' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $item->current_quantity += $request->quantity;
        $item->save();

        return back()->with('success', 'Item restocked successfully.');
    }

    public function destroy(Inventory $item)
    {
        $item->delete();
        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item deleted successfully.');
    }

    public function export()
    {
        $inventory = Inventory::all();

        $csv = "Name,Category,Current Quantity,Minimum Quantity,Unit,Unit Price,Supplier,Value\n";

        foreach ($inventory as $item) {
            $value = $item->current_quantity * $item->unit_cost;
            $csv .= "\"{$item->name}\",{$item->category},{$item->current_quantity},{$item->minimum_quantity},{$item->unit},{$item->unit_price},\"{$item->supplier}\",{$value}\n";
        }

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="inventory-export.csv"');
    }
}

<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    /**
     * Get all tables
     */
    public function index(Request $request)
    {
        $query = Table::orderBy('table_number', 'asc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by capacity
        if ($request->filled('min_capacity')) {
            $query->where('capacity', '>=', $request->min_capacity);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('table_number', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $tables = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $tables
        ]);
    }

    /**
     * Get single table
     */
    public function show(Table $table)
    {
        $table->load(['reservations' => function ($query) {
            $query->whereDate('reservation_date', '>=', today())
                  ->orderBy('reservation_date', 'asc')
                  ->orderBy('reservation_time', 'asc')
                  ->limit(10);
        }]);

        return response()->json([
            'success' => true,
            'data' => $table
        ]);
    }

    /**
     * Create table
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_number' => 'required|string|unique:tables,table_number',
            'capacity' => 'required|integer|min:1|max:20',
            'location' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'status' => 'nullable|in:available,occupied,reserved,maintenance',
        ]);

        $validated['status'] = $validated['status'] ?? 'available';

        $table = Table::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Table created successfully',
            'data' => $table
        ], 201);
    }

    /**
     * Update table
     */
    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'table_number' => 'required|string|unique:tables,table_number,' . $table->id,
            'capacity' => 'required|integer|min:1|max:20',
            'location' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'status' => 'nullable|in:available,occupied,reserved,maintenance',
        ]);

        $table->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Table updated successfully',
            'data' => $table
        ]);
    }

    /**
     * Delete table
     */
    public function destroy(Table $table)
    {
        $hasReservations = Reservation::where('table_id', $table->id)
            ->whereDate('reservation_date', '>=', today())
            ->exists();

        if ($hasReservations) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete table with upcoming reservations'
            ], 400);
        }

        $table->delete();

        return response()->json([
            'success' => true,
            'message' => 'Table deleted successfully'
        ]);
    }

    /**
     * Update status
     */
    public function updateStatus(Request $request, Table $table)
    {
        $request->validate([
            'status' => 'required|in:available,occupied,reserved,maintenance'
        ]);

        $table->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated',
            'data' => $table
        ]);
    }

    /**
     * Get available tables for reservation
     */
    public function getAvailableTables(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time' => 'required|string',
            'guests' => 'required|integer|min:1',
            'exclude_table_id' => 'nullable|exists:tables,id'
        ]);

        $date = $request->date;
        $time = $request->time;
        $guests = $request->guests;
        $excludeTableId = $request->exclude_table_id;

        $tables = Table::where('capacity', '>=', $guests)
            ->where('status', 'available')
            ->when($excludeTableId, function ($query) use ($excludeTableId) {
                $query->where('id', '!=', $excludeTableId);
            })
            ->get();

        $reservedTableIds = Reservation::where('reservation_date', $date)
            ->where('reservation_time', $time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('table_id')
            ->toArray();

        $availableTables = $tables->reject(function ($table) use ($reservedTableIds) {
            return in_array($table->id, $reservedTableIds);
        });

        return response()->json([
            'success' => true,
            'data' => $availableTables->values()
        ]);
    }

    /**
     * Get table statistics
     */
    public function statistics()
    {
        $today = today();
        $todayReservations = Reservation::where('reservation_date', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        $upcomingReservations = Reservation::where('reservation_date', '>', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_tables' => Table::count(),
                'available' => Table::where('status', 'available')->count(),
                'occupied' => Table::where('status', 'occupied')->count(),
                'reserved' => Table::where('status', 'reserved')->count(),
                'maintenance' => Table::where('status', 'maintenance')->count(),
                'today_reservations' => $todayReservations,
                'upcoming_reservations' => $upcomingReservations,
            ]
        ]);
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'table_ids' => 'required|array',
            'table_ids.*' => 'exists:tables,id',
            'status' => 'required|in:available,occupied,reserved,maintenance'
        ]);

        Table::whereIn('id', $request->table_ids)
            ->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => count($request->table_ids) . ' tables updated'
        ]);
    }
}

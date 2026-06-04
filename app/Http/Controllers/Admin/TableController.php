<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Table::query();

        if ($request->has('status') && $request->status) {
            if ($request->status === 'available') {
                $query->whereDoesntHave('reservations', function($q) {
                    $q->whereIn('status', ['pending', 'confirmed'])
                      ->whereDate('reservation_date', today());
                });
            } elseif ($request->status === 'occupied') {
                $query->whereHas('reservations', function($q) {
                    $q->whereIn('status', ['pending', 'confirmed', 'seated'])
                      ->whereDate('reservation_date', today());
                });
            }
        }

        if ($request->has('capacity') && $request->capacity) {
            $query->where('capacity', '>=', $request->capacity);
        }

        $tables = $query->latest()->paginate(20);

        $stats = [
            'total' => Table::count(),
            'available' => Table::whereDoesntHave('reservations', function($q) {
                $q->whereIn('status', ['pending', 'confirmed', 'seated'])
                  ->whereDate('reservation_date', today());
            })->count(),
            'occupied' => Table::whereHas('reservations', function($q) {
                $q->whereIn('status', ['pending', 'confirmed', 'seated'])
                  ->whereDate('reservation_date', today());
            })->count(),
            'maintenance' => Table::where('status', 'maintenance')->count(),
        ];

        return view('admin.tables.index', compact('tables', 'stats'));
    }

    public function create()
    {
        return view('admin.tables.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'table_number' => 'required|string|unique:tables,table_number',
            'capacity' => 'required|integer|min:1',
            'location' => 'nullable|in:indoor,outdoor,patio,private,vip',
            'status' => 'nullable|in:available,occupied,maintenance',
        ]);

        Table::create($request->all());

        return redirect()->route('admin.tables.index')
            ->with('success', 'Table added successfully.');
    }

    public function show(Table $table)
    {
        $table->load(['reservations' => function($query) {
            $query->whereDate('reservation_date', '>=', today())
                  ->orderBy('reservation_date')
                  ->orderBy('reservation_time')
                  ->limit(10);
        }]);

        return view('admin.tables.show', compact('table'));
    }

    public function edit(Table $table)
    {
        return view('admin.tables.edit', compact('table'));
    }

    public function update(Request $request, Table $table)
    {
        $request->validate([
            'table_number' => 'required|string|unique:tables,table_number,' . $table->id,
            'capacity' => 'required|integer|min:1',
        ]);

        $table->update($request->all());

        return redirect()->route('admin.tables.index')
            ->with('success', 'Table updated successfully.');
    }

    public function destroy(Table $table)
    {
        $table->delete();
        return redirect()->route('admin.tables.index')
            ->with('success', 'Table deleted successfully.');
    }

    public function toggleStatus(Request $request, Table $table)
    {
        $statuses = ['available', 'occupied', 'maintenance'];
        $currentIndex = array_search($table->status, $statuses);
        $newStatus = $statuses[($currentIndex + 1) % count($statuses)];

        $table->update(['status' => $newStatus]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => "Status changed to {$newStatus}."
            ]);
        }

        return back()->with('success', "Status changed to {$newStatus}.");
    }

    public function floorPlan()
    {
        $tables = Table::all();
        $reservations = Reservation::whereDate('reservation_date', today())
            ->whereIn('status', ['pending', 'confirmed', 'seated'])
            ->with('user', 'table')
            ->get();

        return view('admin.tables.floor-plan', compact('tables', 'reservations'));
    }

    public function availability(Request $request)
    {
        $date = $request->get('date', today());
        $time = $request->get('time', now()->format('H:i'));
        $partySize = $request->get('party_size', 2);

        $tables = Table::where('capacity', '>=', $partySize)->get();

        $bookedTableIds = Reservation::whereDate('reservation_date', $date)
            ->where('reservation_time', '<=', $time)
            ->whereRaw('DATE_ADD(reservation_time, INTERVAL 2 HOUR) >= ?', [$time])
            ->whereIn('status', ['pending', 'confirmed', 'seated'])
            ->pluck('table_id');

        $availableTables = $tables->whereNotIn('id', $bookedTableIds);

        return response()->json([
            'date' => $date,
            'time' => $time,
            'party_size' => $partySize,
            'available_tables' => $availableTables->values(),
        ]);
    }
}

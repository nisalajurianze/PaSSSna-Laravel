<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Reservation::with('user')->latest();

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('date') && $request->date) {
            $query->whereDate('reservation_date', $request->date);
        }

        $reservations = $query->paginate(20);

        $statusCounts = [
            'pending' => Reservation::where('status', 'pending')->count(),
            'confirmed' => Reservation::where('status', 'confirmed')->count(),
            'cancelled' => Reservation::where('status', 'cancelled')->count(),
            'completed' => Reservation::where('status', 'completed')->count(),
        ];

        return view('admin.reservations.index', compact('reservations', 'statusCounts'));
    }

    public function today()
    {
        $today = Carbon::today();

        $todayReservations = Reservation::whereDate('reservation_date', $today)
            ->with('user')
            ->orderBy('reservation_time')
            ->get();

        $pendingCount = Reservation::whereDate('reservation_date', $today)->where('status', 'pending')->count();
        $confirmedCount = Reservation::whereDate('reservation_date', $today)->where('status', 'confirmed')->count();
        $totalGuests = Reservation::whereDate('reservation_date', $today)->where('status', 'confirmed')->sum('guest_count');

        return view('admin.reservations.today', compact(
            'todayReservations', 'pendingCount', 'confirmedCount', 'totalGuests'
        ));
    }

    public function calendar(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);

        $startOfMonth = Carbon::create($year, $month)->startOfMonth();
        $endOfMonth = Carbon::create($year, $month)->endOfMonth();

        $reservations = Reservation::whereBetween('reservation_date', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->reservation_date)->format('Y-m-d');
            });

        return view('admin.reservations.calendar', compact('reservations', 'month', 'year'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('user');
        return view('admin.reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        $customers = User::where('role', 'customer')->get();
        $availableTables = Table::where('status', 'available')->get();
        return view('admin.reservations.edit', compact('reservation', 'customers', 'availableTables'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required',
            'guest_count' => 'required|integer|min:1',
            'table_count' => 'required|integer|min:1',
            'status' => 'nullable|in:pending,confirmed,cancelled,completed',
            'special_requests' => 'nullable|string',
        ]);

        $reservation->update([
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'reservation_date' => $request->reservation_date,
            'reservation_time' => $request->reservation_time,
            'number_of_people' => $request->guest_count,
            'table_count' => $request->table_count,
            'status' => $request->status ?? $reservation->status,
            'special_requests' => $request->special_requests,
        ]);

        return redirect()->route('admin.reservations.show', $reservation->id)
            ->with('success', 'Reservation updated successfully.');
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $oldStatus = $reservation->status;
        $newStatus = $request->status;

        $reservation->update(['status' => $newStatus]);

        // Update table availability
        if ($newStatus === 'confirmed' && $oldStatus !== 'confirmed') {
            $reservation->update(['tables_reserved' => $reservation->table_count]);
        } elseif ($newStatus === 'cancelled' && $oldStatus === 'confirmed') {
            $reservation->update(['tables_reserved' => 0]);
        } elseif ($newStatus === 'completed') {
            $reservation->update(['tables_reserved' => 0]);
        }

        return back()->with('success', 'Reservation status updated successfully.');
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        $request->validate([
            'cancellation_reason' => 'nullable|string'
        ]);

        $reservation->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->cancellation_reason,
            'tables_reserved' => 0
        ]);

        return back()->with('success', 'Reservation cancelled successfully.');
    }

    public function create()
    {
        $customers = User::where('role', 'customer')->get();
        $availableTables = Table::where('status', 'available')->get();
        return view('admin.reservations.create', compact('customers', 'availableTables'));
    }

    public function storeManual(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string',
            'reservation_date' => 'required|date|after:today',
            'reservation_time' => 'required',
            'guest_count' => 'required|integer|min:1',
            'table_count' => 'required|integer|min:1',
        ]);

        // Find or create customer
        $user = User::where('email', $request->customer_email)->first();
        if (!$user) {
            $user = User::create([
                'name' => $request->customer_name,
                'email' => $request->customer_email,
                'phone' => $request->customer_phone,
                'password' => bcrypt('password'),
                'role' => 'customer'
            ]);
        }

        // Generate unique reservation number
        $reservationNumber = 'RES-' . strtoupper(uniqid());

        $reservation = Reservation::create([
            'reservation_number' => $reservationNumber,
            'user_id' => $user->id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'reservation_date' => $request->reservation_date,
            'reservation_time' => $request->reservation_time,
            'number_of_people' => $request->guest_count,
            'table_count' => $request->table_count,
            'table_numbers' => [],
            'status' => 'confirmed',
            'special_requests' => $request->special_requests,
        ]);

        return redirect()->route('admin.reservations.index')
            ->with('success', 'Reservation created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return redirect()->route('admin.reservations.index')
            ->with('success', 'Reservation deleted successfully.');
    }
}

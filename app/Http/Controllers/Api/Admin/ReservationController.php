<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;

class ReservationController extends Controller
{
    /**
     * Get all reservations
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'table']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('date', $request->date);
        }

        $reservations = $query->orderBy('date', 'asc')->orderBy('time', 'asc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

    /**
     * Update reservation status
     */
    public function updateStatus(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed'
        ]);

        $reservation->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Reservation status updated',
            'data' => $reservation
        ]);
    }
}

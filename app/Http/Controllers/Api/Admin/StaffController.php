<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    /**
     * Get all staff with filters
     */
    public function index(Request $request)
    {
        $query = Staff::with('shift');

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $staff = $query->orderBy('name')->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $staff
        ]);
    }

    /**
     * Get single staff member
     */
    public function show(Staff $staff)
    {
        $staff->load('shift');

        return response()->json([
            'success' => true,
            'data' => $staff
        ]);
    }

    /**
     * Create new staff
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:chef,waiter,manager,bartender,cleaner',
            'shift_id' => 'nullable|exists:shifts,id',
            'status' => 'nullable|in:active,on_leave,inactive',
            'password' => 'required|string|min:6',
            'address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:20',
            'join_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $staff = Staff::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Staff created successfully',
            'data' => $staff
        ], 201);
    }

    /**
     * Update staff
     */
    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:staff,email,' . $staff->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'sometimes|in:chef,waiter,manager,bartender,cleaner',
            'shift_id' => 'nullable|exists:shifts,id',
            'status' => 'nullable|in:active,on_leave,inactive',
            'password' => 'nullable|string|min:6',
            'address' => 'nullable|string|max:500',
            'emergency_contact' => 'nullable|string|max:20',
            'join_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
        ]);

        if ($request->has('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $staff->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Staff updated successfully',
            'data' => $staff
        ]);
    }

    /**
     * Delete staff
     */
    public function destroy(Staff $staff)
    {
        $staff->delete();

        return response()->json([
            'success' => true,
            'message' => 'Staff deleted successfully'
        ]);
    }

    /**
     * Toggle status
     */
    public function toggleStatus(Staff $staff)
    {
        $newStatus = $staff->status === 'active' ? 'inactive' : 'active';
        $staff->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated',
            'data' => $staff
        ]);
    }

    /**
     * Get staff statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => Staff::count(),
            'active' => Staff::where('status', 'active')->count(),
            'on_leave' => Staff::where('status', 'on_leave')->count(),
            'inactive' => Staff::where('status', 'inactive')->count(),
            'by_role' => Staff::groupBy('role')
                ->selectRaw('role, COUNT(*) as count')
                ->get(),
            'today_staff' => Staff::where('status', 'active')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get shifts
     */
    public function getShifts()
    {
        $shifts = Shift::all();

        return response()->json([
            'success' => true,
            'data' => $shifts
        ]);
    }
}

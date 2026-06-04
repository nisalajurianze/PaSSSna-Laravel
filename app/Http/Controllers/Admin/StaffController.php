<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\ShiftSchedule;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Staff::query();

        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $staff = $query->latest()->paginate(20);

        $roles = ['chef', 'manager', 'waiter', 'bartender', 'host', 'cashier', 'delivery_boy', 'cleaner', 'admin'];

        $stats = [
            'total' => Staff::count(),
            'active' => Staff::where('status', 'active')->count(),
            'on_leave' => Staff::where('status', 'on_leave')->count(),
            'inactive' => Staff::where('status', 'inactive')->count(),
        ];

        return view('admin.staff.index', compact('staff', 'roles', 'stats'));
    }

    public function create()
    {
        $roles = ['chef', 'manager', 'waiter', 'bartender', 'host', 'cashier', 'delivery_boy', 'cleaner', 'admin'];
        return view('admin.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'nullable|string',
            'role' => 'required|in:chef,manager,waiter,bartender,host,cashier,delivery_boy,cleaner,admin',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string',
            'emergency_phone' => 'nullable|string',
            'password' => 'required|string|min:6',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['employee_id'] = 'EMP' . strtoupper(substr($request->first_name, 0, 3)) . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        Staff::create($data);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member added successfully.');
    }

    public function show(Staff $staff)
    {
        // Load shifts without date filter (table may not have date column)
        $shifts = $staff->shifts()->limit(10)->get();

        $performance = [
            'total_shifts' => $staff->shifts()->count(),
            'avg_rating' => 4.5, // This would come from a performance_reviews table
        ];

        return view('admin.staff.show', compact('staff', 'performance', 'shifts'));
    }

    public function edit(Staff $staff)
    {
        $roles = ['chef', 'manager', 'waiter', 'bartender', 'host', 'cashier', 'delivery_boy', 'cleaner', 'admin'];
        return view('admin.staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $staff->id,
            'role' => 'required|in:chef,manager,waiter,bartender,host,cashier,delivery_boy,cleaner,admin',
            'salary' => 'required|numeric|min:0',
        ]);

        $data = $request->all();

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        $staff->update($data);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member deleted successfully.');
    }

    public function toggleStatus(Staff $staff)
    {
        $statuses = ['active', 'on_leave', 'inactive'];
        $currentIndex = array_search($staff->status, $statuses);
        $newStatus = $statuses[($currentIndex + 1) % count($statuses)];

        $staff->update(['status' => $newStatus]);

        return back()->with('success', "Status changed to {$newStatus}.");
    }

    public function schedule()
    {
        $today = Carbon::today();
        $weekStart = $today->copy()->startOfWeek();
        $weekEnd = $today->copy()->endOfWeek();

        $staff = Staff::where('status', 'active')->get();

        $schedules = ShiftSchedule::whereBetween('date', [$weekStart, $weekEnd])
            ->with('staff')
            ->get()
            ->groupBy('date');

        return view('admin.staff.schedule', compact('staff', 'schedules', 'weekStart', 'weekEnd'));
    }

    public function updateSchedule(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'date' => 'required|date',
            'shift_type' => 'required|in:morning,evening,night',
        ]);

        ShiftSchedule::updateOrCreate(
            ['staff_id' => $request->staff_id, 'date' => $request->date],
            ['shift_type' => $request->shift_type]
        );

        return back()->with('success', 'Schedule updated successfully.');
    }
}

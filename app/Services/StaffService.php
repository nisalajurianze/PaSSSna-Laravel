<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\ShiftSchedule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Auth;

class StaffService
{
    public function createStaff(array $data)
    {
        return DB::transaction(function () use ($data) {
            // First create user account
            $user = User::create([
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? 'password123'),
                'role' => 'staff',
                'phone' => $data['phone'],
                'address' => $data['address'],
                'is_active' => true,
            ]);

            // Generate employee ID
            $employeeId = $this->generateEmployeeId();

            // Create staff record
            $staff = Staff::create([
                'user_id' => $user->id,
                'employee_id' => $employeeId,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'role' => $data['role'],
                'department' => $data['department'],
                'shift' => $data['shift'],
                'salary' => $data['salary'],
                'hourly_rate' => $data['hourly_rate'],
                'hire_date' => $data['hire_date'],
                'status' => Staff::STATUS_ACTIVE,
                'address' => $data['address'],
                'emergency_contact_name' => $data['emergency_contact_name'],
                'emergency_contact_phone' => $data['emergency_contact_phone'],
                'document_number' => $data['document_number'],
                'document_type' => $data['document_type'],
                'notes' => $data['notes'],
            ]);

            return $staff;
        });
    }

    public function updateStaff(Staff $staff, array $data)
    {
        return DB::transaction(function () use ($staff, $data) {
            // Update staff record
            $staff->update([
                'first_name' => $data['first_name'] ?? $staff->first_name,
                'last_name' => $data['last_name'] ?? $staff->last_name,
                'phone' => $data['phone'] ?? $staff->phone,
                'role' => $data['role'] ?? $staff->role,
                'department' => $data['department'] ?? $staff->department,
                'shift' => $data['shift'] ?? $staff->shift,
                'salary' => $data['salary'] ?? $staff->salary,
                'hourly_rate' => $data['hourly_rate'] ?? $staff->hourly_rate,
                'status' => $data['status'] ?? $staff->status,
                'address' => $data['address'] ?? $staff->address,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? $staff->emergency_contact_name,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? $staff->emergency_contact_phone,
                'notes' => $data['notes'] ?? $staff->notes,
            ]);

            // Update user record if email changed
            if (isset($data['email']) && $data['email'] !== $staff->email) {
                $staff->user->update([
                    'email' => $data['email'],
                    'name' => $data['first_name'] . ' ' . $data['last_name'],
                ]);
            }

            return $staff;
        });
    }

    public function terminateStaff(Staff $staff, $terminationDate, $reason)
    {
        $staff->status = Staff::STATUS_TERMINATED;
        $staff->termination_date = $terminationDate;
        $staff->notes = ($staff->notes ?? '') . "\nTermination: " . $reason;
        $staff->save();

        // Deactivate user account
        $staff->user->is_active = false;
        $staff->user->save();

        return $staff;
    }

    public function scheduleShift($staffId, array $shiftData)
    {
        return ShiftSchedule::create([
            'staff_id' => $staffId,
            'shift_date' => $shiftData['shift_date'],
            'start_time' => $shiftData['start_time'],
            'end_time' => $shiftData['end_time'],
            'shift_type' => $shiftData['shift_type'] ?? ShiftSchedule::TYPE_REGULAR,
            'status' => ShiftSchedule::STATUS_PENDING,
            'notes' => $shiftData['notes'] ?? null,
            'created_by' => Auth::user()?->id,
        ]);
    }

    public function approveShift(ShiftSchedule $shift, $approverId)
    {
        $shift->approve($approverId);
        return $shift;
    }

    public function rejectShift(ShiftSchedule $shift, $reason, $rejectorId)
    {
        $shift->reject($reason, $rejectorId);
        return $shift;
    }

    private function generateEmployeeId()
    {
        $year = date('Y');
        $count = Staff::whereYear('hire_date', $year)->count() + 1;
        return 'EMP' . $year . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function getActiveStaffCount()
    {
        return Staff::active()->count();
    }

    public function getStaffOnLeave()
    {
        return Staff::where('status', Staff::STATUS_ON_LEAVE)->count();
    }

    public function getTodayActiveStaff()
    {
        return Staff::where('status', Staff::STATUS_ACTIVE)
            ->whereHas('shiftSchedules', function ($query) {
                $query->whereDate('shift_date', today())
                      ->where('status', ShiftSchedule::STATUS_APPROVED);
            })
            ->with(['shiftSchedules' => function ($query) {
                $query->whereDate('shift_date', today())
                      ->where('status', ShiftSchedule::STATUS_APPROVED);
            }])
            ->get();
    }

    public function calculateTotalWages($startDate, $endDate)
    {
        $staff = Staff::active()->get();
        $totalWages = 0;

        foreach ($staff as $employee) {
            $totalWages += $employee->calculateWages($startDate, $endDate);
        }

        return $totalWages;
    }

    public function getStaffPerformance($staffId, $startDate, $endDate)
    {
        $staff = Staff::findOrFail($staffId);

        // Get assigned orders
        $assignedOrders = $staff->assignedOrders()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        // Get completed shifts
        $completedShifts = $staff->shiftSchedules()
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->where('status', ShiftSchedule::STATUS_COMPLETED)
            ->get();

        $performance = [
            'staff' => $staff,
            'orders_assigned' => $assignedOrders->count(),
            'orders_completed' => $assignedOrders->where('status', 'completed')->count(),
            'orders_pending' => $assignedOrders->whereIn('status', ['pending', 'confirmed', 'preparing'])->count(),
            'shifts_completed' => $completedShifts->count(),
            'total_hours_worked' => $staff->getTotalHoursWorked($startDate, $endDate),
            'attendance_rate' => $this->calculateAttendanceRate($staff, $startDate, $endDate),
            'customer_rating' => $this->getCustomerRating($staff, $startDate, $endDate),
        ];

        return $performance;
    }

    private function calculateAttendanceRate($staff, $startDate, $endDate)
    {
        $scheduledShifts = $staff->shiftSchedules()
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'completed'])
            ->count();

        $attendedShifts = $staff->shiftSchedules()
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        return $scheduledShifts > 0 ? ($attendedShifts / $scheduledShifts) * 100 : 0;
    }

    private function getCustomerRating($staff, $startDate, $endDate)
    {
        // This would typically come from customer feedback/reviews
        // For now, return a placeholder
        return 4.5;
    }

    public function getStaffSchedule($staffId, $startDate, $endDate)
    {
        return ShiftSchedule::where('staff_id', $staffId)
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->orderBy('shift_date')
            ->get();
    }

    public function generateRoster($startDate, $endDate, $department = null)
    {
        $query = Staff::active();

        if ($department) {
            $query->where('department', $department);
        }

        $staff = $query->get();
        $roster = [];

        $currentDate = \Carbon\Carbon::parse($startDate);
        $endDate = \Carbon\Carbon::parse($endDate);

        while ($currentDate <= $endDate) {
            $dayRoster = [
                'date' => $currentDate->format('Y-m-d'),
                'day' => $currentDate->format('l'),
                'shifts' => [],
            ];

            foreach ($staff as $employee) {
                $shift = $employee->shiftSchedules()
                    ->whereDate('shift_date', $currentDate)
                    ->where('status', ShiftSchedule::STATUS_APPROVED)
                    ->first();

                if ($shift) {
                    $dayRoster['shifts'][] = [
                        'staff' => $employee,
                        'shift' => $shift,
                        'hours' => $shift->net_hours,
                    ];
                }
            }

            $roster[] = $dayRoster;
            $currentDate->addDay();
        }

        return $roster;
    }

    public function calculateOvertime($staffId, $startDate, $endDate, $regularHours = 8)
    {
        $staff = Staff::findOrFail($staffId);
        $shifts = $staff->shiftSchedules()
            ->whereBetween('shift_date', [$startDate, $endDate])
            ->where('status', ShiftSchedule::STATUS_COMPLETED)
            ->get();

        $totalOvertime = 0;
        $overtimeWages = 0;

        foreach ($shifts as $shift) {
            $overtime = $shift->calculateOvertime($regularHours);
            $totalOvertime += $overtime;
            $overtimeWages += $overtime * $staff->hourly_rate * 1.5; // Time and a half
        }

        return [
            'total_overtime_hours' => $totalOvertime,
            'overtime_wages' => $overtimeWages,
            'regular_wages' => $staff->calculateWages($startDate, $endDate) - $overtimeWages,
        ];
    }
}

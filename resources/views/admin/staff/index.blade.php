@extends('layouts.admin')

@section('title', 'Staff')
@section('header', 'Staff Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex gap-2">
            <a href="{{ route('admin.staff.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-user-plus mr-2"></i>Add Staff
            </a>
            <a href="{{ route('admin.staff.schedule') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-calendar-alt mr-2"></i>Schedule
            </a>
        </div>

        <form class="flex gap-2">
            <input type="text" name="search" placeholder="Search staff..."
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                value="{{ request('search') }}">
            <select name="role" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                @endforeach
            </select>
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
            <p class="text-sm text-gray-500">Total Staff</p>
        </div>
        <div class="bg-green-50 rounded-lg shadow-sm p-4 border border-green-100 text-center">
            <p class="text-2xl font-bold text-green-800">{{ $stats['active'] ?? 0 }}</p>
            <p class="text-sm text-green-600">Active</p>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow-sm p-4 border border-yellow-100 text-center">
            <p class="text-2xl font-bold text-yellow-800">{{ $stats['on_leave'] ?? 0 }}</p>
            <p class="text-sm text-yellow-600">On Leave</p>
        </div>
        <div class="bg-gray-100 rounded-lg shadow-sm p-4 border border-gray-200 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['inactive'] ?? 0 }}</p>
            <p class="text-sm text-gray-600">Inactive</p>
        </div>
    </div>

    <!-- Staff Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($staff as $member)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-gray-600 font-medium">{{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">{{ $member->first_name }} {{ $member->last_name }}</p>
                                    <p class="text-sm text-gray-500">Hired: {{ \Carbon\Carbon::parse($member->hire_date)->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                            {{ $member->employee_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">
                                {{ ucfirst(str_replace('_', ' ', $member->role)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-600">{{ $member->email }}</p>
                            <p class="text-sm text-gray-500">{{ $member->phone }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-800">
                            {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($member->salary, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button onclick="toggleStatus({{ $member->id }})"
                                class="px-2 py-1 text-xs rounded-full cursor-pointer transition
                                @switch($member->status)
                                    @case('active') bg-green-100 text-green-700 hover:bg-green-200 @break
                                    @case('on_leave') bg-yellow-100 text-yellow-700 hover:bg-yellow-200 @break
                                    @case('inactive') bg-gray-100 text-gray-700 hover:bg-gray-200 @break
                                @endswitch">
                                {{ ucfirst(str_replace('_', ' ', $member->status)) }}
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.staff.show', $member) }}" class="text-red-600 hover:text-red-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.staff.edit', $member) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.staff.destroy', $member) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                            <p>No staff members found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $staff->links() }}
        </div>
    </div>
</div>

<script>
function toggleStatus(staffId) {
    fetch(`/admin/staff/${staffId}/toggle`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endsection


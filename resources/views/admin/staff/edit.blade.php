@extends('layouts.admin')

@section('title', 'Edit Staff')
@section('header', 'Edit Staff')

@section('content')
<div class="space-y-6">
    <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $staff->first_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $staff->last_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $staff->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $staff->phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                        @php $roles = ['chef', 'manager', 'waiter', 'bartender', 'host', 'cashier', 'delivery_boy', 'cleaner', 'admin']; @endphp
                        @foreach($roles as $role)
                        <option value="{{ $role }}" @selected(old('role', $staff->role) == $role)>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salary</label>
                    <input type="number" name="salary" value="{{ old('salary', $staff->salary) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" min="0" required>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.staff.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Update Staff</button>
        </div>
    </form>
</div>
@endsection


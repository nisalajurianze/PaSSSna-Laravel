@extends('layouts.admin')

@section('title', 'Staff Details')
@section('header', 'Staff Details')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">{{ $staff->first_name }} {{ $staff->last_name }}</h3>
            <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium capitalize">{{ str_replace('_', ' ', $staff->role) }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Contact Information</h4>
                <p class="font-medium">{{ $staff->first_name }} {{ $staff->last_name }}</p>
                <p class="text-gray-600">{{ $staff->email }}</p>
                <p class="text-gray-600">{{ $staff->phone ?? 'N/A' }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Employment Details</h4>
                <p><span class="text-gray-500">Role:</span> {{ ucfirst(str_replace('_', ' ', $staff->role)) }}</p>
                <p><span class="text-gray-500">Salary:</span> {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($staff->salary, 2) }}</p>
                <p><span class="text-gray-500">Joined:</span> {{ $staff->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('admin.staff.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Back</a>
        <a href="{{ route('admin.staff.edit', $staff->id) }}" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Edit</a>
    </div>
</div>
@endsection


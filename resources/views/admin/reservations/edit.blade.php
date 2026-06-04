@extends('layouts.admin')

@section('title', 'Edit Reservation')
@section('header', 'Edit Reservation')

@section('content')
<div class="space-y-6">
    <form action="{{ route('admin.reservations.update', $reservation->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', $reservation->customer_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email', $reservation->customer_email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone', $reservation->customer_phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Reservation Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="reservation_date" value="{{ old('reservation_date', $reservation->reservation_date->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                    <input type="time" name="reservation_time" value="{{ old('reservation_time', $reservation->reservation_time) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of Guests</label>
                    <input type="number" name="guest_count" value="{{ old('guest_count', $reservation->number_of_people) }}" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Table Count</label>
                    <input type="number" name="table_count" value="{{ old('table_count', $reservation->table_count) }}" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Status</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reservation Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="pending" @selected(old('status', $reservation->status) == 'pending')>Pending</option>
                    <option value="confirmed" @selected(old('status', $reservation->status) == 'confirmed')>Confirmed</option>
                    <option value="completed" @selected(old('status', $reservation->status) == 'completed')>Completed</option>
                    <option value="cancelled" @selected(old('status', $reservation->status) == 'cancelled')>Cancelled</option>
                </select>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Special Requests</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Special Requests</label>
                <textarea name="special_requests" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('special_requests', $reservation->special_requests) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Update Reservation</button>
        </div>
    </form>
</div>
@endsection


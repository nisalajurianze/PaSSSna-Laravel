@extends('layouts.admin')

@section('title', 'Reservation Details')
@section('header', 'Reservation Details')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Reservation #{{ $reservation->reservation_number }}</h3>
            <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium">{{ ucfirst($reservation->status) }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Customer Information</h4>
                <p class="font-medium">{{ $reservation->customer_name }}</p>
                <p class="text-gray-600">{{ $reservation->customer_email }}</p>
                <p class="text-gray-600">{{ $reservation->customer_phone }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Reservation Details</h4>
                <p><span class="text-gray-500">Date:</span> {{ $reservation->reservation_date->format('M d, Y') }}</p>
                <p><span class="text-gray-500">Time:</span> {{ $reservation->reservation_time }}</p>
                <p><span class="text-gray-500">Guests:</span> {{ $reservation->number_of_people }}</p>
                <p><span class="text-gray-500">Tables:</span> {{ $reservation->table_count }}</p>
            </div>
        </div>

        @if($reservation->special_requests)
        <div class="mt-6">
            <h4 class="text-sm font-medium text-gray-500 mb-2">Special Requests</h4>
            <p class="text-gray-600 bg-gray-50 p-4 rounded-lg">{{ $reservation->special_requests }}</p>
        </div>
        @endif
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('admin.reservations.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Back</a>
        <form action="{{ route('admin.reservations.updateStatus', $reservation) }}" method="POST" class="flex gap-2">
            @csrf
            @method('PATCH')
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="pending" @selected($reservation->status == 'pending')>Pending</option>
                <option value="confirmed" @selected($reservation->status == 'confirmed')>Confirmed</option>
                <option value="completed" @selected($reservation->status == 'completed')>Completed</option>
                <option value="cancelled" @selected($reservation->status == 'cancelled')>Cancelled</option>
            </select>
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Update Status</button>
        </form>
    </div>
</div>
@endsection


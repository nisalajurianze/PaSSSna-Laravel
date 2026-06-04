@extends("layouts.admin")
@section("title", "Create Reservation")
@section("header", "Create New Reservation")
@section("content")
<div class="space-y-6">
    <form action="{{ route("admin.reservations.storeManual") }}" method="POST">
        @csrf
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                    <input type="text" name="customer_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="customer_email" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="customer_phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Reservation Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reservation Date</label>
                    <input type="date" name="reservation_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reservation Time</label>
                    <input type="time" name="reservation_time" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Guest Count</label>
                    <input type="number" name="guest_count" class="w-full px-4 py-2 border border-gray-300 rounded-lg" min="1" value="2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Table Count</label>
                    <input type="number" name="table_count" class="w-full px-4 py-2 border border-gray-300 rounded-lg" min="1" value="1" required>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Select Table</h3>
            @if($availableTables && count($availableTables) > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($availableTables as $table)
                <label class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="table_id" value="{{ $table->id }}" class="mr-2">
                    <span class="font-medium">Table {{ $table->table_number }}</span>
                    <span class="text-sm text-gray-500 block">{{ $table->capacity }} seats</span>
                </label>
                @endforeach
            </div>
            @else
            <p class="text-gray-500">No available tables at the moment.</p>
            @endif
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Special Requests</h3>
            <textarea name="special_requests" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Any special requests or notes..."></textarea>
        </div>
        
        <div class="flex justify-end gap-4">
            <a href="{{ route("admin.reservations.index") }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Create Reservation</button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Reservations')
@section('header', 'Reservation Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex gap-2">
            <a href="{{ route('admin.reservations.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-plus mr-2"></i>New Reservation
            </a>
            <a href="{{ route('admin.reservations.calendar') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-calendar-alt mr-2"></i>Calendar View
            </a>
        </div>

        <form class="flex gap-2">
            <input type="date" name="date"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                value="{{ request('date') ?? today()->format('Y-m-d') }}">
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
            <p class="text-sm text-gray-500">Total</p>
        </div>
        <div class="bg-blue-50 rounded-lg shadow-sm p-4 border border-blue-100 text-center">
            <p class="text-2xl font-bold text-blue-800">{{ $stats['pending'] ?? 0 }}</p>
            <p class="text-sm text-blue-600">Pending</p>
        </div>
        <div class="bg-green-50 rounded-lg shadow-sm p-4 border border-green-100 text-center">
            <p class="text-2xl font-bold text-green-800">{{ $stats['confirmed'] ?? 0 }}</p>
            <p class="text-sm text-green-600">Confirmed</p>
        </div>
        <div class="bg-purple-50 rounded-lg shadow-sm p-4 border border-purple-100 text-center">
            <p class="text-2xl font-bold text-purple-800">{{ $stats['completed'] ?? 0 }}</p>
            <p class="text-sm text-purple-600">Completed</p>
        </div>
    </div>

    <!-- Reservations Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guests</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Table</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Source</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($reservations as $reservation)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ $reservation->user->name ?? $reservation->guest_name }}</p>
                            <p class="text-sm text-gray-500">{{ $reservation->user->email ?? $reservation->guest_email }}</p>
                            <p class="text-sm text-gray-500">{{ $reservation->guest_phone }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-gray-800">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('M d, Y') }}</p>
                            <p class="text-sm text-gray-500">{{ $reservation->reservation_time }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                            <i class="fas fa-users mr-1 text-gray-400"></i>{{ $reservation->guests }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($reservation->table)
                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">
                                    Table {{ $reservation->table->table_number }}
                                </span>
                            @else
                                <span class="text-gray-400">Not assigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <select onchange="updateStatus({{ $reservation->id }}, this.value)"
                                class="text-xs px-2 py-1 rounded-full border-0 cursor-pointer
                                @switch($reservation->status)
                                    @case('pending') bg-yellow-100 text-yellow-700 @break
                                    @case('confirmed') bg-blue-100 text-blue-700 @break
                                    @case('seated') bg-green-100 text-green-700 @break
                                    @case('completed') bg-gray-100 text-gray-700 @break
                                    @case('cancelled') bg-red-100 text-red-700 @break
                                    @case('no-show') bg-orange-100 text-orange-700 @break
                                @endswitch">
                                <option value="pending" {{ $reservation->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $reservation->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="seated" {{ $reservation->status == 'seated' ? 'selected' : '' }}>Seated</option>
                                <option value="completed" {{ $reservation->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $reservation->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="no-show" {{ $reservation->status == 'no-show' ? 'selected' : '' }}>No Show</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ucfirst($reservation->source ?? 'website') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.reservations.show', $reservation) }}" class="text-red-600 hover:text-red-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.reservations.edit', $reservation) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.reservations.destroy', $reservation) }}" method="POST" class="inline">
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
                            <i class="fas fa-calendar-alt text-4xl mb-4 text-gray-300"></i>
                            <p>No reservations found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $reservations->links() }}
        </div>
    </div>
</div>

<script>
function updateStatus(reservationId, status) {
    fetch(`/admin/reservations/${reservationId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status })
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


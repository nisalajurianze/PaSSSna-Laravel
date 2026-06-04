@extends('layouts.app')

@section('title', 'My Reservations - PaSSSna Restaurant')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-800">My Reservations</h1>
                <p class="text-gray-600 mt-2">Track your upcoming and past reservations.</p>
            </div>
            <a href="{{ route('reservation.create') }}"
               class="inline-flex items-center justify-center bg-gradient-to-r from-primary-red to-primary-yellow text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition duration-300">
                <i class="fas fa-plus mr-2"></i>Book a Table
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6 border-b">
                <form class="grid grid-cols-1 md:grid-cols-3 gap-4" method="GET" action="{{ route('customer.reservations') }}">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-red">
                            <option value="">All</option>
                            @foreach(['pending','confirmed','rejected','cancelled','completed','no_show'] as $s)
                                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-red">
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                                class="w-full bg-gray-800 text-white px-6 py-2 rounded-lg font-semibold hover:bg-gray-900 transition duration-300">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>

            @if($reservations->count() === 0)
                <div class="p-10 text-center">
                    <div class="w-20 h-20 mx-auto mb-5 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No reservations found</h3>
                    <p class="text-gray-600 mb-6">Try a different filter or book a new reservation.</p>
                    <a href="{{ route('reservation.create') }}"
                       class="inline-flex items-center bg-primary-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition duration-300">
                        <i class="fas fa-calendar-plus mr-2"></i>Book Now
                    </a>
                </div>
            @else
                <div class="divide-y">
                    @foreach($reservations as $reservation)
                        <div class="p-6 hover:bg-gray-50 transition duration-300">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-lg font-bold text-gray-800">
                                            {{ $reservation->reservation_number ?? ('Reservation #' . $reservation->id) }}
                                        </h3>
                                        <span class="px-3 py-1 text-xs rounded-full font-semibold
                                            @if($reservation->status === 'confirmed') bg-green-100 text-green-800
                                            @elseif($reservation->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif(in_array($reservation->status, ['rejected','cancelled'])) bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst(str_replace('_',' ',$reservation->status)) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-2">
                                        <i class="fas fa-calendar-day mr-2 text-primary-red"></i>
                                        {{ $reservation->reservation_date->format('M d, Y') }}
                                        <span class="mx-2 text-gray-300">•</span>
                                        <i class="fas fa-clock mr-2 text-primary-red"></i>
                                        {{ \Carbon\Carbon::parse($reservation->reservation_time)->format('g:i A') }}
                                    </p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-users mr-2 text-primary-red"></i>
                                        {{ $reservation->number_of_people }} guests
                                        <span class="mx-2 text-gray-300">•</span>
                                        <i class="fas fa-chair mr-2 text-primary-red"></i>
                                        Tables: {{ $reservation->tables->pluck('table_number')->implode(', ') ?: collect($reservation->table_numbers)->implode(', ') }}
                                    </p>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-2">
                                    <a href="{{ route('customer.reservations.show', $reservation) }}"
                                       class="px-5 py-2 rounded-lg border border-gray-200 text-gray-800 font-semibold hover:bg-white transition duration-300">
                                        <i class="fas fa-eye mr-2"></i>Details
                                    </a>

                                    @if($reservation->canBeCancelled())
                                        <form method="POST" action="{{ route('customer.reservations.cancel', $reservation) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="px-5 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition duration-300"
                                                    onclick="return confirm('Cancel this reservation?')">
                                                <i class="fas fa-times mr-2"></i>Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($reservations->hasPages())
                    <div class="p-6 border-t">
                        {{ $reservations->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection



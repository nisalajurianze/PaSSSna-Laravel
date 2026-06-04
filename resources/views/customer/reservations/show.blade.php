@extends('layouts.app')

@section('title', 'Reservation Details - PaSSSna Restaurant')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('customer.reservations') }}" class="text-gray-700 hover:text-primary-red font-semibold">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
            <span class="px-3 py-1 text-xs rounded-full font-semibold
                @if($reservation->status === 'confirmed') bg-green-100 text-green-800
                @elseif($reservation->status === 'pending') bg-yellow-100 text-yellow-800
                @elseif(in_array($reservation->status, ['rejected','cancelled'])) bg-red-100 text-red-800
                @else bg-gray-100 text-gray-800 @endif">
                {{ ucfirst(str_replace('_',' ',$reservation->status)) }}
            </span>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-primary-red to-primary-yellow p-6 text-white">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h1 class="text-2xl font-bold">
                            {{ $reservation->reservation_number ?? ('Reservation #' . $reservation->id) }}
                        </h1>
                        <p class="text-white/90 mt-1">{{ $reservation->customer_name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-white/80">Tables</p>
                        <p class="text-lg font-extrabold">
                            {{ $reservation->tables->pluck('table_number')->implode(', ') ?: collect($reservation->table_numbers)->implode(', ') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 rounded-xl p-5">
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="text-lg font-bold text-gray-800">{{ $reservation->reservation_date->format('M d, Y') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-5">
                        <p class="text-sm text-gray-500">Time</p>
                        <p class="text-lg font-bold text-gray-800">{{ \Carbon\Carbon::parse($reservation->reservation_time)->format('g:i A') }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-5">
                        <p class="text-sm text-gray-500">Guests</p>
                        <p class="text-lg font-bold text-gray-800">{{ $reservation->number_of_people }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-5">
                        <p class="text-sm text-gray-500">Contact</p>
                        <p class="text-sm text-gray-800"><i class="fas fa-envelope mr-2 text-primary-red"></i>{{ $reservation->customer_email }}</p>
                        <p class="text-sm text-gray-800 mt-1"><i class="fas fa-phone mr-2 text-primary-red"></i>{{ $reservation->customer_phone }}</p>
                    </div>
                </div>

                @if($reservation->special_requests)
                    <div class="mt-6 bg-gradient-to-r from-yellow-50 to-red-50 border border-yellow-100 rounded-xl p-5">
                        <p class="text-sm text-gray-700">
                            <span class="font-semibold"><i class="fas fa-sticky-note text-primary-red mr-2"></i>Special requests:</span>
                            {{ $reservation->special_requests }}
                        </p>
                    </div>
                @endif

                @if($reservation->confirmation_message)
                    <div class="mt-6 bg-green-50 border border-green-200 rounded-xl p-5">
                        <p class="text-sm text-green-900">
                            <span class="font-semibold"><i class="fas fa-check-circle text-green-600 mr-2"></i>Restaurant message:</span>
                            {{ $reservation->confirmation_message }}
                        </p>
                    </div>
                @endif

                @if($reservation->cancellation_reason && in_array($reservation->status, ['rejected','cancelled']))
                    <div class="mt-6 bg-red-50 border border-red-200 rounded-xl p-5">
                        <p class="text-sm text-red-900">
                            <span class="font-semibold"><i class="fas fa-info-circle text-red-600 mr-2"></i>Message:</span>
                            {{ $reservation->cancellation_reason }}
                        </p>
                    </div>
                @endif

                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('menu') }}"
                       class="flex-1 text-center bg-gray-800 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-900 transition duration-300">
                        <i class="fas fa-utensils mr-2"></i>Go to Menu
                    </a>

                    @if($reservation->canBeCancelled())
                        <form class="flex-1" method="POST" action="{{ route('customer.reservations.cancel', $reservation) }}">
                            @csrf
                            <button type="submit"
                                    class="w-full bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition duration-300"
                                    onclick="return confirm('Cancel this reservation?')">
                                <i class="fas fa-times mr-2"></i>Cancel Reservation
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



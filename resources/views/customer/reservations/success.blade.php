@extends('layouts.app')

@section('title', 'Reservation Submitted - PaSSSna Restaurant')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden animate-slide-up">
            <div class="bg-gradient-to-r from-primary-red to-primary-yellow p-8 text-white">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">Reservation Submitted</h1>
                        <p class="text-white/90 mt-2">We’ll review and confirm your reservation shortly.</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-white/80">Reservation #</p>
                        <p class="text-xl font-extrabold">{{ $reservation->reservation_number ?? ('#' . $reservation->id) }}</p>
                    </div>
                </div>
            </div>

            <div class="p-8">
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
                        <p class="text-sm text-gray-500">Tables</p>
                        <p class="text-lg font-bold text-gray-800">
                            {{ $reservation->tables->pluck('table_number')->implode(', ') ?: collect($reservation->table_numbers)->implode(', ') }}
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                        @if($reservation->status === 'confirmed') bg-green-100 text-green-800
                        @elseif($reservation->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif(in_array($reservation->status, ['rejected','cancelled'])) bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        <i class="fas fa-circle mr-2 text-xs"></i>
                        Status: {{ ucfirst($reservation->status) }}
                    </span>
                </div>

                @if($reservation->special_requests)
                <div class="mt-6 bg-gradient-to-r from-yellow-50 to-red-50 border border-yellow-100 rounded-xl p-5">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-sticky-note text-primary-red mr-2"></i>
                        <span class="font-semibold">Special requests:</span>
                        {{ $reservation->special_requests }}
                    </p>
                </div>
                @endif

                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('menu') }}"
                       class="flex-1 text-center bg-gradient-to-r from-primary-red to-primary-yellow text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition duration-300">
                        <i class="fas fa-utensils mr-2"></i>View Menu
                    </a>
                    @auth
                        <a href="{{ route('customer.reservations') }}"
                           class="flex-1 text-center border-2 border-gray-200 text-gray-800 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition duration-300">
                            <i class="fas fa-calendar-alt mr-2"></i>My Reservations
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="flex-1 text-center border-2 border-gray-200 text-gray-800 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition duration-300">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                    @endauth
                </div>

                <div class="mt-8 text-sm text-gray-500">
                    <p><i class="fas fa-info-circle text-primary-red mr-2"></i>If you need changes, please contact us via the Contact page.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



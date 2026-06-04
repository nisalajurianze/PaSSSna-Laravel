@extends('layouts.admin')

@section('title', 'Reservation Calendar')
@section('header', 'Reservation Calendar')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div class="flex gap-4">
            <a href="{{ route('admin.reservations.calendar', ['month' => $month - 1 <= 0 ? 12 : $month - 1, 'year' => $month - 1 <= 0 ? $year - 1 : $year]) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Previous Month</a>
            <h3 class="text-xl font-semibold text-gray-800">{{ \Carbon\Carbon::create($year, $month)->format('F Y') }}</h3>
            <a href="{{ route('admin.reservations.calendar', ['month' => $month + 1 > 12 ? 1 : $month + 1, 'year' => $month + 1 > 12 ? $year + 1 : $year]) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Next Month</a>
        </div>
        <a href="{{ route('admin.reservations.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">New Reservation</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-7 gap-2 mb-4">
            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
            <div class="text-center font-semibold text-gray-600 py-2">{{ $day }}</div>
            @endforeach
        </div>

        <div class="grid grid-cols-7 gap-2">
            @php
            $firstDayOfMonth = \Carbon\Carbon::create($year, $month)->startOfMonth();
            $daysInMonth = \Carbon\Carbon::create($year, $month)->daysInMonth;
            $startDay = $firstDayOfMonth->dayOfWeek;
            $today = \Carbon\Carbon::today()->format('Y-m-d');
            @endphp

            @for($i = 0; $i < $startDay; $i++)
            <div class="p-4 border rounded-lg bg-gray-50"></div>
            @endfor

            @for($day = 1; $day <= $daysInMonth; $day++)
            @php
            $date = \Carbon\Carbon::create($year, $month, $day)->format('Y-m-d');
            $dayReservations = isset($reservations[$date]) ? $reservations[$date] : collect();
            $isToday = $date === $today;
            @endphp
            <div class="p-4 border rounded-lg {{ $isToday ? 'bg-blue-50 border-blue-300' : 'hover:bg-gray-50' }}">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold {{ $isToday ? 'text-blue-600' : 'text-gray-700' }}">{{ $day }}</span>
                    @if($dayReservations->count() > 0)
                    <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">{{ $dayReservations->count() }}</span>
                    @endif
                </div>
                <div class="space-y-1">
                    @foreach($dayReservations->take(3) as $reservation)
                    <a href="{{ route('admin.reservations.show', $reservation) }}" class="block text-xs p-1 rounded truncate {{ $reservation->status === 'confirmed' ? 'bg-green-100 text-green-800' : ($reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ $reservation->reservation_time }} - {{ $reservation->customer_name }}
                    </a>
                    @endforeach
                    @if($dayReservations->count() > 3)
                    <div class="text-xs text-gray-500">+{{ $dayReservations->count() - 3 }} more</div>
                    @endif
                </div>
            </div>
            @endfor
        </div>
    </div>

    <div class="flex gap-4 text-sm">
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 bg-green-100 rounded"></span>
            <span>Confirmed</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 bg-yellow-100 rounded"></span>
            <span>Pending</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 bg-gray-100 rounded"></span>
            <span>Other</span>
        </div>
    </div>
</div>
@endsection


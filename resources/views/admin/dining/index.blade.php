@extends('layouts.admin')

@section('title', 'Dining')
@section('header', 'Dining Section')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Active Tables</h2>
            <p class="text-sm text-gray-500">Monitor live dining sessions and table status.</p>
        </div>
        <a href="{{ route('dining.login') }}" target="_blank" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
            <i class="fas fa-external-link-alt mr-2"></i>Open Dining Login
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($tables as $table)
            @php
                $session = $activeSessions->get($table->table_number);
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm text-gray-500">Table</p>
                        <p class="text-2xl font-semibold text-gray-800">#{{ $table->table_number }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        {{ $session ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $session ? 'Active' : 'Available' }}
                    </span>
                </div>

                <div class="text-sm text-gray-600 space-y-1">
                    <p><strong>Capacity:</strong> {{ $table->capacity ?? 'N/A' }}</p>
                    <p><strong>Area:</strong> {{ $table->area ?? 'Main' }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($table->status) }}</p>
                </div>

                @if($session)
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600"><strong>Session:</strong> {{ $session->session_code }}</p>
                        <p class="text-sm text-gray-600"><strong>Orders:</strong> {{ $session->orders_count }}</p>
                        <p class="text-sm text-gray-600"><strong>Started:</strong> {{ $session->start_time?->format('M d, H:i') }}</p>
                    </div>

                    <form method="POST" action="{{ route('admin.dining.close', $session) }}" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                            <i class="fas fa-door-closed mr-2"></i>Close Table
                        </button>
                    </form>
                @else
                    <div class="mt-4 text-sm text-gray-500">
                        No active dining session.
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection

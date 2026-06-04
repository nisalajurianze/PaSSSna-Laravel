@extends('layouts.app', ['kiosk' => true])

@section('title', 'Dining Login')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#F3EEE7] via-white to-[#F8F3ED] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-4xl bg-white/80 backdrop-blur rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2">
            <div class="p-8 lg:p-12 bg-gradient-to-br from-primary-red/10 to-primary-yellow/20">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-primary-red text-white rounded-2xl flex items-center justify-center text-xl font-bold">P</div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Dining Section</h1>
                        <p class="text-sm text-gray-500">Enter your table number to start ordering.</p>
                    </div>
                </div>

                <div class="space-y-4 text-sm text-gray-700">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-utensils text-primary-red mt-1"></i>
                        <p>Browse the full menu, build custom meals, and send orders directly to the kitchen.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fas fa-lightbulb text-primary-yellow mt-1"></i>
                        <p>Smart recommendations update in real time as the menu and stock change.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fas fa-lock text-navy-blue mt-1"></i>
                        <p>Tables are closed by staff only with an admin password for safety.</p>
                    </div>
                </div>
            </div>

            <div class="p-8 lg:p-12">
                <form method="POST" action="{{ route('dining.login.submit') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Table Number</label>
                        <input type="number" name="table_number" min="1" required
                               value="{{ old('table_number') }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary-red focus:ring-2 focus:ring-primary-red/20">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Guests (optional)</label>
                        <input type="number" name="guests" min="1" max="20"
                               value="{{ old('guests') }}"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary-red focus:ring-2 focus:ring-primary-red/20">
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-primary-red to-red-600 text-white font-semibold hover:from-red-700 hover:to-red-800 transition">
                        Start Dining Session
                    </button>
                </form>

                @if($tables->count() > 0)
                    <div class="mt-8">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Table Availability</h3>
                        <div class="grid grid-cols-4 gap-2 text-xs">
                            @foreach($tables as $table)
                                @php $isActive = in_array($table->table_number, $activeTables ?? [], true); @endphp
                                <div class="px-2 py-2 rounded-lg text-center border {{ $isActive ? 'bg-red-50 border-red-200 text-red-700' : 'bg-green-50 border-green-200 text-green-700' }}">
                                    <div class="font-semibold">#{{ $table->table_number }}</div>
                                    <div>{{ $isActive ? 'Active' : 'Free' }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

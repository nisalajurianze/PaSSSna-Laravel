@extends('layouts.admin')

@section('title', 'Tables')
@section('header', 'Table Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex gap-2">
            <a href="{{ route('admin.tables.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-plus mr-2"></i>Add Table
            </a>
            <a href="{{ route('admin.tables.floorPlan') }}" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                <i class="fas fa-th-large mr-2"></i>Floor Plan
            </a>
        </div>

        <form class="flex gap-2">
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">All Status</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
            <select name="capacity" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">All Capacities</option>
                <option value="2" {{ request('capacity') == '2' ? 'selected' : '' }}>2+ seats</option>
                <option value="4" {{ request('capacity') == '4' ? 'selected' : '' }}>4+ seats</option>
                <option value="6" {{ request('capacity') == '6' ? 'selected' : '' }}>6+ seats</option>
                <option value="8" {{ request('capacity') == '8' ? 'selected' : '' }}>8+ seats</option>
            </select>
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
            <p class="text-sm text-gray-500">Total Tables</p>
        </div>
        <div class="bg-green-50 rounded-lg shadow-sm p-4 border border-green-100 text-center">
            <p class="text-2xl font-bold text-green-800">{{ $stats['available'] ?? 0 }}</p>
            <p class="text-sm text-green-600">Available</p>
        </div>
        <div class="bg-red-50 rounded-lg shadow-sm p-4 border border-red-100 text-center">
            <p class="text-2xl font-bold text-red-800">{{ $stats['occupied'] ?? 0 }}</p>
            <p class="text-sm text-red-600">Occupied</p>
        </div>
        <div class="bg-gray-100 rounded-lg shadow-sm p-4 border border-gray-200 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['maintenance'] ?? 0 }}</p>
            <p class="text-sm text-gray-600">Maintenance</p>
        </div>
    </div>

    <!-- Tables Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
        @forelse($tables as $table)
            <div class="bg-white rounded-xl shadow-sm border-2 overflow-hidden hover:shadow-md transition
                @switch($table->status)
                    @case('available') border-green-400 @break
                    @case('occupied') border-red-400 @break
                    @case('maintenance') border-gray-400 @break
                @endswitch">
                <div class="p-4 text-center
                    @switch($table->status)
                        @case('available') bg-green-50 @break
                        @case('occupied') bg-red-50 @break
                        @case('maintenance') bg-gray-100 @break
                    @endswitch">
                    <div class="text-3xl font-bold text-gray-800">{{ $table->table_number }}</div>
                    <div class="text-sm text-gray-500 mt-1">
                        <i class="fas fa-users mr-1"></i>{{ $table->capacity }} seats
                    </div>
                </div>

                <div class="p-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs px-2 py-1 rounded-full
                            @switch($table->status)
                                @case('available') bg-green-100 text-green-700 @break
                                @case('occupied') bg-red-100 text-red-700 @break
                                @case('maintenance') bg-gray-100 text-gray-700 @break
                            @endswitch">
                            {{ ucfirst($table->status) }}
                        </span>
                        <span class="text-xs text-gray-500">{{ $table->location ?? 'Main' }}</span>
                    </div>

                    <div class="flex gap-1">
                        <form action="{{ route('admin.tables.toggle', $table->id) }}" method="POST" style="display: inline; width: 33%;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition" title="Change Status">
                                <i class="fas fa-sync-alt mr-1"></i>Change
                            </button>
                        </form>
                        <a href="{{ route('admin.tables.edit', $table) }}" class="flex-1 px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition text-center" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST" style="display: inline; width: 33%;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 transition" title="Delete" onclick="return confirm('Are you sure you want to delete this table?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <i class="fas fa-chair text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No tables found</h3>
                    <p class="text-gray-500 mb-4">Get started by adding your first table</p>
                    <a href="{{ route('admin.tables.create') }}" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-plus mr-2"></i>Add Table
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection


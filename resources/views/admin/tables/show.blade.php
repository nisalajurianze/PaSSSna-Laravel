@extends('layouts.admin')

@section('title', 'Table Details')
@section('header', 'Table Details')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">Table {{ $table->table_number }}</h3>
            <span class="px-4 py-2 rounded-full text-sm font-medium @if($table->status == 'available') bg-green-100 text-green-800 @elseif($table->status == 'occupied') bg-red-100 text-red-800 @else bg-yellow-100 text-yellow-800 @endif">
                {{ ucfirst($table->status) }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Capacity</h4>
                <p class="text-lg font-semibold">{{ $table->capacity }} guests</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Location</h4>
                <p class="text-lg font-semibold capitalize">{{ $table->location }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500 mb-2">Created</h4>
                <p class="text-lg font-semibold">{{ $table->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <div class="flex justify-end gap-4">
        <a href="{{ route('admin.tables.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Back</a>
        <a href="{{ route('admin.tables.edit', $table->id) }}" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Edit</a>
        <form action="{{ route('admin.tables.toggle', $table->id) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Toggle Status
            </button>
        </form>
    </div>
</div>
@endsection


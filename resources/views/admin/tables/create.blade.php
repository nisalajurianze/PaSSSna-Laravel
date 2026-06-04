@extends('layouts.admin')

@section('title', 'Add Table')
@section('header', 'Add New Table')

@section('content')
<div class="space-y-6">
    <form action="{{ route('admin.tables.store') }}" method="POST">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Table Number</label>
                    <input type="text" name="table_number" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="e.g., T1, 101" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                    <input type="number" name="capacity" class="w-full px-4 py-2 border border-gray-300 rounded-lg" min="1" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <select name="location" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="indoor">Indoor</option>
                        <option value="outdoor">Outdoor</option>
                        <option value="patio">Patio</option>
                        <option value="private">Private</option>
                        <option value="vip">VIP</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="available">Available</option>
                        <option value="occupied">Occupied</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.tables.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Add Table</button>
        </div>
    </form>
</div>
@endsection


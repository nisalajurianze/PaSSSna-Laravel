@extends('layouts.admin')

@section('title', 'Inventory Details')
@section('header', 'Inventory Details')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-800">{{ $item->name }}</h3>
            <span class="px-4 py-2 {{ $item->current_quantity <= $item->minimum_quantity ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }} rounded-full text-sm font-medium">
                {{ $item->current_quantity <= $item->minimum_quantity ? 'Low Stock' : 'In Stock' }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500 mb-1">Category</p>
                <p class="font-semibold text-gray-800">{{ $item->category }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500 mb-1">Unit</p>
                <p class="font-semibold text-gray-800">{{ strtoupper($item->unit) }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500 mb-1">Current Quantity</p>
                <p class="font-semibold text-gray-800">{{ number_format($item->current_quantity, 2) }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500 mb-1">Minimum Quantity</p>
                <p class="font-semibold text-gray-800">{{ number_format($item->minimum_quantity, 2) }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500 mb-1">Unit Cost</p>
                <p class="font-semibold text-gray-800">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->unit_cost, 2) }}</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500 mb-1">Total Value</p>
                <p class="font-semibold text-gray-800">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->total_value, 2) }}</p>
            </div>
            @if($item->supplier)
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500 mb-1">Supplier</p>
                <p class="font-semibold text-gray-800">{{ $item->supplier }}</p>
            </div>
            @endif
            @if($item->expiry_date)
            <div class="p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500 mb-1">Expiry Date</p>
                <p class="font-semibold text-gray-800">{{ $item->expiry_date->format('M d, Y') }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Stock Status Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h4 class="text-md font-semibold text-gray-800 mb-4">Stock Level</h4>
        <div class="relative h-8 bg-gray-200 rounded-full overflow-hidden">
            @php
                $percentage = min(100, ($item->current_quantity / max($item->minimum_quantity * 2, 1)) * 100);
                $color = $item->current_quantity <= $item->minimum_quantity ? 'bg-yellow-500' : 'bg-green-500';
            @endphp
            <div class="absolute top-0 left-0 h-full {{ $color }} transition-all duration-300" style="width: {{ $percentage }}%"></div>
        </div>
        <div class="flex justify-between mt-2 text-sm text-gray-500">
            <span>0</span>
            <span>Minimum: {{ number_format($item->minimum_quantity, 2) }}</span>
            <span>Current: {{ number_format($item->current_quantity, 2) }}</span>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-4">
        <a href="{{ route('admin.inventory.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
            Back to List
        </a>
        <a href="{{ route('admin.inventory.edit', $item->id) }}" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
            Edit Item
        </a>
    </div>
</div>
@endsection


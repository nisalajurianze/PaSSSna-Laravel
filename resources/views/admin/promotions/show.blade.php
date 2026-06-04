@extends('layouts.admin')

@section('title', 'Promotion Details')
@section('header', 'Promotion Details')

@section('content')
<div class="space-y-6">
    <!-- Basic Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-start mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Basic Information</h3>
            <a href="{{ route('admin.promotions.edit', $promotion) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">Edit</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500 mb-1">Promotion Name</p>
                <p class="font-semibold text-gray-800">{{ $promotion->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Promotion Code</p>
                <p class="font-semibold text-gray-800">{{ $promotion->code }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Description</p>
                <p class="text-gray-800">{{ $promotion->description ?? 'No description' }}</p>
            </div>
        </div>
    </div>

    <!-- Discount Settings -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Discount Settings</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500 mb-1">Promotion Type</p>
                <p class="font-semibold text-gray-800">{{ ucfirst(str_replace('_', ' ', $promotion->promotion_type ?? 'N/A')) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Discount Value</p>
                <p class="font-semibold text-gray-800">
                    @if($promotion->promotion_type == 'percentage')
                        {{ $promotion->discount_value }}%
                    @else
                        Rs. {{ number_format($promotion->discount_value, 2) }}
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Minimum Order Amount</p>
                <p class="font-semibold text-gray-800">Rs. {{ number_format($promotion->minimum_order_amount ?? 0, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Maximum Discount</p>
                <p class="font-semibold text-gray-800">Rs. {{ number_format($promotion->maximum_discount ?? 0, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Usage Limit</p>
                <p class="font-semibold text-gray-800">{{ $promotion->usage_limit ?? 'Unlimited' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Times Used</p>
                <p class="font-semibold text-gray-800">{{ $promotion->times_used ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Validity Period -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Validity Period</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500 mb-1">Start Date</p>
                <p class="font-semibold text-gray-800">{{ $promotion->start_date ? $promotion->start_date->format('Y-m-d') : 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">End Date</p>
                <p class="font-semibold text-gray-800">{{ $promotion->end_date ? $promotion->end_date->format('Y-m-d') : 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Status -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Status</h3>
        <div class="flex items-center gap-2">
            @if($promotion->is_active)
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Active</span>
            @else
                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">Inactive</span>
            @endif
        </div>
    </div>

    <!-- Recent Usage -->
    @if(isset($promotion->usage) && $promotion->usage->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Usage</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($promotion->usage as $order)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-800">#{{ $order->id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $order->customer_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-800">Rs. {{ number_format($order->total_amount, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $order->created_at->format('Y-m-d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="flex justify-start">
        <a href="{{ route('admin.promotions.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Back to Promotions</a>
    </div>
</div>
@endsection

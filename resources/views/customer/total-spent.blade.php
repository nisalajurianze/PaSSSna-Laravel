@extends('layouts.app')

@section('title', 'Total Spent - PaSSSna Restaurant')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 to-yellow-50 py-10">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Total Spent</h1>
                <p class="text-gray-600">Your completed spending history</p>
            </div>
            <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center text-primary-red hover:text-red-700 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-red-100">
                <div class="text-sm text-gray-500 mb-2">Total Spent</div>
                <div class="text-3xl font-bold text-primary-red">
                    {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($totalSpent, 2) }}
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-yellow-100">
                <div class="text-sm text-gray-500 mb-2">Completed Orders</div>
                <div class="text-3xl font-bold text-gray-800">{{ $totalOrders }}</div>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-green-100">
                <div class="text-sm text-gray-500 mb-2">Average Order</div>
                <div class="text-3xl font-bold text-green-600">
                    {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($averageOrder, 2) }}
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Order History</h2>
            </div>

            @if($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">Order</th>
                                <th class="px-4 py-3 text-left">Date</th>
                                <th class="px-4 py-3 text-left">Type</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-4 py-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $order->order_number }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $order->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $order->order_type_text }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-primary-red">
                                        {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->total ?? $order->total_amount, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                            @if($order->status == 'completed' || $order->status == 'delivered' || $order->status == 'served')
                                                bg-green-100 text-green-700
                                            @else
                                                bg-gray-100 text-gray-600
                                            @endif">
                                            {{ $order->status_text }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-10">
                    <i class="fas fa-receipt text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">No completed orders yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection


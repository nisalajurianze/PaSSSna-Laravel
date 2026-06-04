@extends('layouts.app')

@section('title', 'My Orders - PaSSSna Restaurant')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-800">My Orders</h1>
                <p class="text-gray-600 mt-2">View your order history and download invoices.</p>
            </div>
            <a href="{{ route('menu') }}"
               class="inline-flex items-center justify-center bg-gradient-to-r from-primary-red to-primary-yellow text-white px-6 py-3 rounded-lg font-semibold hover:opacity-90 transition duration-300">
                <i class="fas fa-utensils mr-2"></i>Browse Menu
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6 border-b">
                <form class="grid grid-cols-1 md:grid-cols-3 gap-4" method="GET" action="{{ route('customer.orders') }}">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-red">
                            <option value="">All</option>
                            @foreach(['pending','confirmed','preparing','ready','served','completed','cancelled','out_for_delivery','delivered'] as $s)
                                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Order Type</label>
                        <select name="order_type" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-red">
                            <option value="">All</option>
                            @foreach(['delivery' => 'Delivery', 'takeaway' => 'Takeaway', 'dine_in' => 'Dine In'] as $value => $label)
                                <option value="{{ $value }}" @selected(request('order_type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit"
                                class="w-full bg-gray-800 text-white px-6 py-2 rounded-lg font-semibold hover:bg-gray-900 transition duration-300">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>

            @if($orders->count() === 0)
                <div class="p-10 text-center">
                    <div class="w-20 h-20 mx-auto mb-5 bg-gray-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-receipt text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">No orders found</h3>
                    <p class="text-gray-600 mb-6">Order something delicious from our menu.</p>
                    <a href="{{ route('menu') }}"
                       class="inline-flex items-center bg-primary-red text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition duration-300">
                        <i class="fas fa-utensils mr-2"></i>Go to Menu
                    </a>
                </div>
            @else
                <div class="divide-y">
                    @foreach($orders as $order)
                        <div class="p-6 hover:bg-gray-50 transition duration-300" data-order-id="{{ $order->id }}" data-status="{{ $order->status }}">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-lg font-bold text-gray-800">
                                            {{ $order->order_number ?? ('Order #' . $order->id) }}
                                        </h3>
                                        <span class="px-3 py-1 text-xs rounded-full font-semibold
                                            @if(in_array($order->status, ['completed','delivered'])) bg-green-100 text-green-800
                                            @elseif(in_array($order->status, ['pending','confirmed','preparing','ready','out_for_delivery','served'])) bg-yellow-100 text-yellow-800
                                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst(str_replace('_',' ',$order->status)) }}
                                        </span>
                                    </div>

                                    <p class="text-sm text-gray-600 mt-2">
                                        <i class="fas fa-calendar-day mr-2 text-primary-red"></i>
                                        {{ $order->created_at?->format('M d, Y') }}
                                        <span class="mx-2 text-gray-300">•</span>
                                        <i class="fas fa-clock mr-2 text-primary-red"></i>
                                        {{ $order->created_at?->format('g:i A') }}
                                    </p>

                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-shopping-bag mr-2 text-primary-red"></i>
                                        {{ $order->order_type_text ?? ucfirst(str_replace('_',' ',$order->order_type)) }}
                                        <span class="mx-2 text-gray-300">•</span>
                                        <i class="fas fa-list mr-2 text-primary-red"></i>
                                        {{ $order->items->count() }} items
                                        <span class="mx-2 text-gray-300">•</span>
                                        <i class="fas fa-coins mr-2 text-primary-red"></i>
                                        {{ $order->formatted_total ?? $order->total }}
                                    </p>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-2">
                                    <a href="{{ route('customer.orders.show', $order) }}"
                                       class="px-5 py-2 rounded-lg border border-gray-200 text-gray-800 font-semibold hover:bg-white transition duration-300">
                                        <i class="fas fa-eye mr-2"></i>Details
                                    </a>

                                    <a href="{{ route('customer.orders.invoice', $order) }}"
                                       class="px-5 py-2 rounded-lg bg-gray-800 text-white font-semibold hover:bg-gray-900 transition duration-300">
                                        <i class="fas fa-file-pdf mr-2"></i>Invoice
                                    </a>

                                    @if($order->canBeCancelled())
                                        <form method="POST" action="{{ route('customer.orders.cancel', $order) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="px-5 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700 transition duration-300"
                                                    onclick="return confirm('Cancel this order?')">
                                                <i class="fas fa-times mr-2"></i>Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($orders->hasPages())
                    <div class="p-6 border-t">
                        {{ $orders->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Order Status Live Update Polling -->
@php
$activeOrders = $orders->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery']);
@endphp

@if($activeOrders->count() > 0)
<script>
    // Real-time order status polling
    let activeOrderIds = @json($activeOrders->pluck('id')->toArray());
    let lastStatusMap = {};

    // Initialize last status for each order
    activeOrderIds.forEach(id => {
        const orderElement = document.querySelector(`[data-order-id="${id}"]`);
        if (orderElement) {
            lastStatusMap[id] = orderElement.dataset.status;
        }
    });

    function checkOrderStatuses() {
        fetch('{{ route("customer.orders") }}')
            .then(response => response.text())
            .then(html => {
                // Parse the response to check for status changes
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                activeOrderIds.forEach(orderId => {
                    const newOrderElement = doc.querySelector(`[data-order-id="${orderId}"]`);
                    const oldStatusElement = document.querySelector(`[data-order-id="${orderId}"]`);

                    if (newOrderElement && oldStatusElement) {
                        const newStatus = newOrderElement.dataset.status;
                        const oldStatus = oldStatusElement.dataset.status;

                        if (newStatus !== oldStatus) {
                            // Status has changed!
                            showToast(`Order #${orderId} status updated to: ${newStatus.replace('_', ' ')}`, 'info');
                            setTimeout(() => location.reload(), 2000);
                        }
                    }
                });
            })
            .catch(error => console.log('Order status check failed:', error));
    }

    // Start polling every 3 seconds if there are active orders
    @if($activeOrders->count() > 0)
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(checkOrderStatuses, 3000);
        setInterval(checkOrderStatuses, 3000);
    });
    @endif
</script>
@endif

<!-- Live Updates Indicator -->
@if($activeOrders->count() > 0)
<div class="fixed bottom-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded-full shadow-lg flex items-center text-sm">
    <span class="relative flex h-3 w-3 mr-2">
        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
    </span>
    Live Order Tracking
</div>
@endif

@endsection



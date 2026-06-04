@extends('layouts.app')

@section('title', 'Order Details - PaSSSna Restaurant')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <a href="{{ route('customer.orders') }}" class="text-sm text-gray-600 hover:text-primary-red">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                </a>
                <h1 class="text-4xl font-bold text-gray-800 mt-2">
                    {{ $order->order_number ?? ('Order #' . $order->id) }}
                </h1>
                <p class="text-gray-600 mt-2">
                    Placed on {{ $order->created_at?->format('M d, Y \a\t g:i A') }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('customer.orders.invoice', $order) }}"
                   class="inline-flex items-center justify-center bg-gray-800 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-900 transition duration-300">
                    <i class="fas fa-file-pdf mr-2"></i>Download Invoice
                </a>

                @if($order->canBeCancelled())
                    <form method="POST" action="{{ route('customer.orders.cancel', $order) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center justify-center bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition duration-300"
                                onclick="return confirm('Cancel this order?')">
                            <i class="fas fa-times mr-2"></i>Cancel Order
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Summary -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Summary</h2>

                    <div class="flex items-center justify-between mb-3">
                        <span class="text-gray-600">Status</span>
                        <span class="px-3 py-1 text-xs rounded-full font-semibold
                            @if(in_array($order->status, ['completed','delivered'])) bg-green-100 text-green-800
                            @elseif(in_array($order->status, ['pending','confirmed','preparing','ready','out_for_delivery','served'])) bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst(str_replace('_',' ',$order->status)) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between mb-3">
                        <span class="text-gray-600">Type</span>
                        <span class="font-semibold text-gray-800">{{ $order->order_type_text ?? ucfirst(str_replace('_',' ',$order->order_type)) }}</span>
                    </div>

                    <div class="flex items-center justify-between mb-3">
                        <span class="text-gray-600">Payment</span>
                        <span class="font-semibold text-gray-800">{{ ucfirst(str_replace('_',' ',$order->payment_method)) }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Payment Status</span>
                        <span class="font-semibold text-gray-800">{{ ucfirst(str_replace('_',' ',$order->payment_status)) }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Totals</h2>

                    <div class="flex items-center justify-between py-2 border-b">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold text-gray-800">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format((float) $order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b">
                        <span class="text-gray-600">Tax</span>
                        <span class="font-semibold text-gray-800">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format((float) $order->tax, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b">
                        <span class="text-gray-600">Delivery</span>
                        <span class="font-semibold text-gray-800">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format((float) $order->delivery_charge, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b">
                        <span class="text-gray-600">Discount</span>
                        <span class="font-semibold text-gray-800">- {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format((float) $order->discount, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-4">
                        <span class="text-gray-800 font-bold">Total</span>
                        <span class="text-primary-red font-bold text-xl">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format((float) $order->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-bold text-gray-800">Items ({{ $order->items->count() }})</h2>
                    </div>

                    <div class="divide-y">
                        @foreach($order->items as $item)
                            <div class="p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div>
                                    <h3 class="font-bold text-gray-800">
                                        {{ $item->name ?? 'Item' }}
                                        @if($item->size)
                                            <span class="text-sm font-semibold text-gray-500">({{ ucfirst($item->size) }})</span>
                                        @endif
                                    </h3>

                                    <p class="text-sm text-gray-600 mt-1">
                                        Qty: <span class="font-semibold">{{ $item->quantity }}</span>
                                        <span class="mx-2 text-gray-300">•</span>
                                        Unit: <span class="font-semibold">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format((float) $item->price, 2) }}</span>
                                    </p>

                                    @if(!empty($item->toppings))
                                        <p class="text-sm text-gray-600 mt-1">
                                            <i class="fas fa-cheese mr-2 text-primary-red"></i>
                                            Toppings: {{ $item->toppings_text }}
                                        </p>
                                    @endif

                                    @if($item->special_instructions)
                                        <p class="text-sm text-gray-600 mt-1">
                                            <i class="fas fa-sticky-note mr-2 text-primary-red"></i>
                                            {{ $item->special_instructions }}
                                        </p>
                                    @endif
                                </div>

                                <div class="text-right">
                                    <p class="text-gray-600 text-sm">Line Total</p>
                                    <p class="text-gray-800 font-bold text-lg">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format((float) $item->total, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($order->special_instructions)
                        <div class="p-6 border-t bg-gray-50">
                            <h3 class="font-bold text-gray-800 mb-2">Order Notes</h3>
                            <p class="text-gray-700">{{ $order->special_instructions }}</p>
                        </div>
                    @endif

                    @if($order->cancellation_reason)
                        <div class="p-6 border-t bg-red-50">
                            <h3 class="font-bold text-red-800 mb-2">Cancellation Reason</h3>
                            <p class="text-red-700">{{ $order->cancellation_reason }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



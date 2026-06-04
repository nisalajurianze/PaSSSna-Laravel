@extends("layouts.admin")

@section("title", "Order Details")
@section("header", "Order Details")

@section("content")
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Order #{{ $order->id }}</h2>
                @if($order->order_number)
                    <p class="text-gray-500 mt-1">Order No: {{ $order->order_number }}</p>
                @endif
                <p class="text-gray-500 mt-1">{{ $order->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <a href="{{ route("admin.orders.index") }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Back</a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-700 mb-2">Customer Info</h4>
                <p class="text-gray-600">{{ $order->customer_name ?? 'N/A' }}</p>
                <p class="text-gray-600">{{ $order->customer_phone ?? 'N/A' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-700 mb-2">Order Info</h4>
                <p class="text-gray-600"><span class="font-medium">Type:</span> {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</p>
                <p class="text-gray-600"><span class="font-medium">Status:</span> {{ ucfirst($order->status) }}</p>
                @if($order->table_number)
                    <p class="text-gray-600"><span class="font-medium">Table:</span> {{ $order->table_number }}</p>
                @endif
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="font-semibold text-gray-700 mb-2">Payment</h4>
                <p class="text-gray-600"><span class="font-medium">Method:</span> {{ ucfirst($order->payment_method) }}</p>
                <p class="text-gray-600"><span class="font-medium">Total:</span> {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->total_amount, 2) }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Order Items</h3>
        <table class="w-full">
            <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
            <tbody>
            @foreach($order->items as $item)
                <tr><td>{{ $item->item_name }}</td><td>{{ $item->quantity }}</td><td>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->unit_price, 2) }}</td><td>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->total_price, 2) }}</td></tr>
            @endforeach
            </tbody>
        </table>
        <div class="mt-4 text-right">
            <p class="text-lg font-semibold">Total: {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->total_amount, 2) }}</p>
        </div>
    </div>
</div>
@endsection

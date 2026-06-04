@extends("layouts.admin")
@section("title", "Edit Order - " . $order->order_number)
@section("header", "Edit Order")
@section("content")
<div class="space-y-6">
    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
        @csrf
        @method("PUT")
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Number</label>
                    <input type="text" value="{{ $order->order_number }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="ready" {{ $order->status == 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="served" {{ $order->status == 'served' ? 'selected' : '' }}>Served</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Type</h3>
            <div class="flex gap-4 mb-4">
                <label class="flex items-center">
                    <input type="radio" name="order_type" value="dine_in" {{ $order->order_type == 'dine_in' ? 'checked' : '' }} class="mr-2"> Dine In
                </label>
                <label class="flex items-center">
                    <input type="radio" name="order_type" value="takeaway" {{ $order->order_type == 'takeaway' ? 'checked' : '' }} class="mr-2"> Takeaway
                </label>
                <label class="flex items-center">
                    <input type="radio" name="order_type" value="delivery" {{ $order->order_type == 'delivery' ? 'checked' : '' }} class="mr-2"> Delivery
                </label>
            </div>
            <div id="tableSelection" style="display: {{ $order->order_type == 'dine_in' ? 'block' : 'none' }};">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Table</label>
                <select name="table_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">-- Select Table --</option>
                    @foreach($tables as $table)
                    <option value="{{ $table->id }}" {{ $order->table_id == $table->id ? 'selected' : '' }}>Table {{ $table->table_number }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="cash" {{ $order->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="card" {{ $order->payment_method == 'card' ? 'selected' : '' }}>Card</option>
                        <option value="online" {{ $order->payment_method == 'online' ? 'selected' : '' }}>Online</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                    <select name="payment_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h3>
            @foreach($order->items as $item)
            <div class="flex items-center justify-between p-3 border rounded-lg mb-2">
                <div>
                    <p class="font-medium">{{ $item->item_name }}</p>
                    <p class="text-sm text-gray-500">Qty: {{ $item->quantity }} x {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->unit_price, 2) }}</p>
                </div>
                <p class="font-medium">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->total_price, 2) }}</p>
            </div>
            @endforeach
            <div class="mt-4 pt-4 border-t">
                <div class="flex justify-between font-medium"><span>Subtotal:</span><span>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->subtotal, 2) }}</span></div>
                <div class="flex justify-between font-medium"><span>Tax:</span><span>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->tax_amount, 2) }}</span></div>
                @if($order->delivery_charge > 0)
                <div class="flex justify-between font-medium"><span>Delivery:</span><span>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->delivery_charge, 2) }}</span></div>
                @endif
                <div class="flex justify-between text-lg font-bold mt-2"><span>Total:</span><span>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->total, 2) }}</span></div>
            </div>
        </div>
        
        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.orders.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Update Order</button>
        </div>
    </form>
</div>
<script>
document.querySelectorAll("input[name=order_type]").forEach(r => {
    r.addEventListener("change", () => {
        document.getElementById("tableSelection").style.display = r.value === 'dine_in' ? 'block' : 'none'
    })
})
</script>
@endsection 


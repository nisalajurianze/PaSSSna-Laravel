@extends('layouts.admin')

@section('title', 'Create Order')
@section('header', 'Create New Order')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">Create New Order</h2>
        <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Orders
        </a>
    </div>
    
    <form action="{{ route('admin.orders.storeManual') }}" method="POST" id="orderForm">
        @csrf
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name *</label>
                    <input type="text" name="customer_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="customer_phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email (optional)</label>
                    <input type="email" name="customer_email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Type</h3>
            <div class="flex gap-4">
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="order_type" value="dine_in" checked class="mr-2">
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">Dine In</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="order_type" value="takeaway" class="mr-2">
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm">Takeaway</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="order_type" value="delivery" class="mr-2">
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Delivery</span>
                </label>
            </div>
            
            <div id="tableSelection" class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Table</label>
                <select name="table_number" class="w-full md:w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="">-- Select Table --</option>
                    @if($tables && count($tables) > 0)
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}">Table {{ $table->table_number }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Items</h3>
            
            @if($menuItems && count($menuItems) > 0)
                <div class="space-y-3">
                    @foreach($menuItems as $item)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-gray-300 transition">
                            <div class="flex items-center">
                                <input type="checkbox" name="items[]" value="{{ $item->id }}" id="item_{{ $item->id }}" class="mr-4 h-5 w-5 text-red-600 focus:ring-red-500">
                                <div>
                                    <label for="item_{{ $item->id }}" class="font-medium text-gray-800 cursor-pointer">{{ $item->name }}</label>
                                    @if($item->description)
                                        <p class="text-sm text-gray-500">{{ $item->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <span class="font-medium text-gray-800">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->price, 2) }}</span>
                                <input type="number" name="quantities[{{ $item->id }}]" value="1" min="1" class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-center" disabled>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No menu items available</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Method</h3>
            <div class="flex gap-4">
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="payment_method" value="cash" checked class="mr-2">
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">Cash</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="payment_method" value="card" class="mr-2">
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">Card</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="payment_method" value="online" class="mr-2">
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">Online</span>
                </label>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.orders.index') }}" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-medium">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                <i class="fas fa-plus mr-2"></i>Create Order
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name="items[]"]').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var input = this.parentElement.parentElement.querySelector('input[name^="quantities"]');
            if (input) {
                input.disabled = !this.checked;
                if (!this.checked) input.value = 1;
            }
        });
    });

    document.querySelectorAll('input[name="order_type"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var tableSelection = document.getElementById('tableSelection');
            if (tableSelection) {
                tableSelection.style.display = this.value === 'dine_in' ? 'block' : 'none';
                var select = tableSelection.querySelector('select');
                if (select) select.disabled = this.value !== 'dine_in';
            }
        });
    });
    
    var dineInRadio = document.querySelector('input[name="order_type"][value="dine_in"]');
    if (dineInRadio && !dineInRadio.checked) {
        var ts = document.getElementById('tableSelection');
        if (ts) { ts.style.display = 'none'; var s = ts.querySelector('select'); if (s) s.disabled = true; }
    }
});
</script>
@endsection


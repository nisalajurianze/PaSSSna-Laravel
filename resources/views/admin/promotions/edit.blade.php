@extends('layouts.admin')

@section('title', 'Edit Promotion')
@section('header', 'Edit Promotion')

@section('content')
<div class="space-y-6">
    <form action="{{ route('admin.promotions.update', $promotion) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Promotion Name</label>
                    <input type="text" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required value="{{ old('name', $promotion->name) }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Promotion Code</label>
                    <input type="text" name="code" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required value="{{ old('code', $promotion->code) }}">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" class="w-full px-4 py-2 border border-gray-300 rounded-lg" rows="3">{{ old('description', $promotion->description) }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Discount Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Promotion Type</label>
                    <select name="promotion_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                        <option value="percentage" @selected($promotion->promotion_type == 'percentage')>Percentage Discount</option>
                        <option value="fixed" @selected($promotion->promotion_type == 'fixed')>Fixed Amount Discount</option>
                        <option value="buy_x_get_y" @selected($promotion->promotion_type == 'buy_x_get_y')>Buy X Get Y</option>
                        <option value="bogo" @selected($promotion->promotion_type == 'bogo')>Buy One Get One (BOGO)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Discount Value</label>
                    <input type="number" name="discount_value" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required min="0" step="0.01" value="{{ old('discount_value', $promotion->discount_value) }}">
                    <p class="text-sm text-gray-500 mt-1">For percentage: 1-100. For fixed: amount in currency.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Order Amount</label>
                    <input type="number" name="minimum_order_amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg" min="0" step="0.01" value="{{ old('minimum_order_amount', $promotion->minimum_order_amount) }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Maximum Discount</label>
                    <input type="number" name="maximum_discount" class="w-full px-4 py-2 border border-gray-300 rounded-lg" min="0" step="0.01" value="{{ old('maximum_discount', $promotion->maximum_discount) }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Usage Limit</label>
                    <input type="number" name="usage_limit" class="w-full px-4 py-2 border border-gray-300 rounded-lg" min="1" value="{{ old('usage_limit', $promotion->usage_limit) }}">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Validity Period</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required value="{{ old('start_date', $promotion->start_date ? $promotion->start_date->format('Y-m-d') : '') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required value="{{ old('end_date', $promotion->end_date ? $promotion->end_date->format('Y-m-d') : '') }}">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Apply To</h3>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="applicable_to" value="all" class="mr-2" @checked($promotion->applicable_to == 'all')> Entire Order
                </label>
                <label class="flex items-center">
                    <input type="radio" name="applicable_to" value="category" class="mr-2" @checked($promotion->applicable_to == 'category')> Specific Category
                </label>
                <label class="flex items-center">
                    <input type="radio" name="applicable_to" value="menu_item" class="mr-2" @checked($promotion->applicable_to == 'menu_item')> Specific Menu Items
                </label>
            </div>

            <div id="categorySelection" class="mt-4 {{ $promotion->applicable_to != 'category' ? 'hidden' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Category</label>
                <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">-- Select Category --</option>
                    @if(isset($categories) && $categories->count() > 0)
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected($promotion->category_id == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    @else
                        <option value="">No categories available</option>
                    @endif
                </select>
            </div>

            <div id="menuItemsSelection" class="mt-4 {{ $promotion->applicable_to != 'menu_item' ? 'hidden' : '' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Menu Items</label>
                <select name="menu_item_ids[]" multiple class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    @if(isset($menuItems) && $menuItems->count() > 0)
                        @php $selectedItems = old('menu_item_ids', $promotion->menu_item_ids ?? []); @endphp
                        @foreach($menuItems as $item)
                            <option value="{{ $item->id }}" @if(in_array($item->id, $selectedItems)) selected @endif>{{ $item->name }}</option>
                        @endforeach
                    @endif
                </select>
                <p class="text-sm text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple items</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Status</h3>
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" class="mr-2" value="1" @checked($promotion->is_active)>
                <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.promotions.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Update Promotion</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const applicableToRadios = document.querySelectorAll('input[name="applicable_to"]');
    const categorySelection = document.getElementById('categorySelection');
    const menuItemsSelection = document.getElementById('menuItemsSelection');

    function updateVisibility() {
        let selectedValue;
        applicableToRadios.forEach(function(radio) {
            if (radio.checked) {
                selectedValue = radio.value;
            }
        });

        categorySelection.classList.add('hidden');
        menuItemsSelection.classList.add('hidden');

        if (selectedValue === 'category') {
            categorySelection.classList.remove('hidden');
        } else if (selectedValue === 'menu_item') {
            menuItemsSelection.classList.remove('hidden');
        }
    }

    applicableToRadios.forEach(function(radio) {
        radio.addEventListener('change', updateVisibility);
    });

    // Initial state
    updateVisibility();
});
</script>
@endsection

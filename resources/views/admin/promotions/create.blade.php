@extends('layouts.admin')

@section('title', 'Create Promotion')
@section('header', 'Create New Promotion')

@section('content')
<div class="space-y-6">
    <form action="{{ route('admin.promotions.store') }}" method="POST">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Promotion Name</label>
                    <input type="text" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Promotion Code</label>
                    <input type="text" name="code" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required placeholder="e.g., SUMMER20">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" class="w-full px-4 py-2 border border-gray-300 rounded-lg" rows="3"></textarea>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Discount Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Promotion Type</label>
                    <select name="promotion_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                        <option value="percentage">Percentage Discount</option>
                        <option value="fixed">Fixed Amount Discount</option>
                        <option value="buy_x_get_y">Buy X Get Y</option>
                        <option value="bogo">Buy One Get One (BOGO)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Discount Value</label>
                    <input type="number" name="discount_value" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required min="0" step="0.01" placeholder="Percentage or fixed amount">
                    <p class="text-sm text-gray-500 mt-1">For percentage: 1-100. For fixed: amount in currency.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Order Amount</label>
                    <input type="number" name="minimum_order_amount" class="w-full px-4 py-2 border border-gray-300 rounded-lg" min="0" step="0.01" placeholder="Optional">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Maximum Discount</label>
                    <input type="number" name="maximum_discount" class="w-full px-4 py-2 border border-gray-300 rounded-lg" min="0" step="0.01" placeholder="Optional">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Usage Limit</label>
                    <input type="number" name="usage_limit" class="w-full px-4 py-2 border border-gray-300 rounded-lg" min="1" placeholder="Optional">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Validity Period</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Apply To</h3>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="radio" name="applicable_to" value="all" checked class="mr-2"> Entire Order
                </label>
                <label class="flex items-center">
                    <input type="radio" name="applicable_to" value="category" class="mr-2"> Specific Category
                </label>
                <label class="flex items-center">
                    <input type="radio" name="applicable_to" value="menu_item" class="mr-2"> Specific Menu Items
                </label>
            </div>

            <div id="categorySelection" class="mt-4 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Category</label>
                <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">-- Select Category --</option>
                    @if(isset($categories) && $categories->count() > 0)
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    @else
                        <option value="">No categories available</option>
                    @endif
                </select>
                @if(!isset($categories) || $categories->count() == 0)
                    @if(\Illuminate\Support\Facades\Route::has('admin.categories.create'))
                        <p class="text-sm text-gray-500 mt-1">
                            <a href="{{ route('admin.categories.create') }}" class="text-red-600 hover:underline">Create a category first</a>
                        </p>
                    @else
                        <p class="text-sm text-gray-500 mt-1">No categories available. Please add categories first.</p>
                    @endif
                @endif
            </div>

            <div id="menuItemsSelection" class="mt-4 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Menu Items</label>
                <select name="menu_item_ids[]" multiple class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    @if(isset($menuItems) && $menuItems->count() > 0)
                        @foreach($menuItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    @endif
                </select>
                <p class="text-sm text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple items</p>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.promotions.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Create Promotion</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const applicableToRadios = document.querySelectorAll('input[name="applicable_to"]');
    const categorySelection = document.getElementById('categorySelection');
    const menuItemsSelection = document.getElementById('menuItemsSelection');

    applicableToRadios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            categorySelection.classList.add('hidden');
            menuItemsSelection.classList.add('hidden');

            if (this.value === 'category') {
                categorySelection.classList.remove('hidden');
            } else if (this.value === 'menu_item') {
                menuItemsSelection.classList.remove('hidden');
            }
        });
    });
});
</script>
@endsection

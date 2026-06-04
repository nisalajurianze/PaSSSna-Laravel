@extends('layouts.admin')

@section('title', 'Edit Inventory Item')
@section('header', 'Edit Inventory Item')

@section('content')
<div class="space-y-6">
    <form action="{{ route('admin.inventory.update', $item->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Basic Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Item Name *</label>
                    <input type="text" name="name" value="{{ old('name', $item->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                        <option value="vegetable" @selected(old('category', $item->category) == 'vegetable')>Vegetables</option>
                        <option value="meat" @selected(old('category', $item->category) == 'meat')>Meat</option>
                        <option value="dairy" @selected(old('category', $item->category) == 'dairy')>Dairy</option>
                        <option value="spice" @selected(old('category', $item->category) == 'spice')>Spices</option>
                        <option value="grain" @selected(old('category', $item->category) == 'grain')>Grains</option>
                        <option value="beverage" @selected(old('category', $item->category) == 'beverage')>Beverages</option>
                        <option value="other" @selected(old('category', $item->category) == 'other')>Other</option>
                    </select>
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit *</label>
                    <select name="unit" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                        <option value="">Select Unit</option>
                        <option value="kg" {{ old('unit', $item->unit) == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                        <option value="g" {{ old('unit', $item->unit) == 'g' ? 'selected' : '' }}>Grams (g)</option>
                        <option value="l" {{ old('unit', $item->unit) == 'l' ? 'selected' : '' }}>Liters (L)</option>
                        <option value="ml" {{ old('unit', $item->unit) == 'ml' ? 'selected' : '' }}>Milliliters (ml)</option>
                        <option value="pcs" {{ old('unit', $item->unit) == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                        <option value="boxes" {{ old('unit', $item->unit) == 'boxes' ? 'selected' : '' }}>Boxes</option>
                        <option value="packs" {{ old('unit', $item->unit) == 'packs' ? 'selected' : '' }}>Packs</option>
                    </select>
                    @error('unit')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <input type="text" name="supplier" value="{{ old('supplier', $item->supplier) }}" placeholder="Supplier name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    @error('supplier')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Quantity & Cost -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quantity & Cost</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Quantity *</label>
                    <input type="number" name="current_quantity" value="{{ old('current_quantity', $item->current_quantity) }}" min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                    @error('current_quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Quantity *</label>
                    <input type="number" name="minimum_quantity" value="{{ old('minimum_quantity', $item->minimum_quantity) }}" min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                    @error('minimum_quantity')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Cost *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                        <input type="number" name="unit_cost" value="{{ old('unit_cost', $item->unit_cost) }}" min="0" step="0.01" class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" required>
                    </div>
                    @error('unit_cost')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.inventory.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                Update Inventory Item
            </button>
        </div>
    </form>
</div>
@endsection


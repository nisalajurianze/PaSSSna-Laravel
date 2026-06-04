@extends('layouts.admin')

@section('title', 'Low Stock Items')
@section('header', 'Low Stock Items')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Low Stock Items</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $lowStockItems->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">In Stock</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Inventory::where('status', 'in_stock')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-arrow-down text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Need Restock</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $lowStockItems->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-boxes text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Items</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Inventory::count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.inventory.lowStock') }}" method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-200">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search items..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <option value="">All Categories</option>
                    <option value="vegetable" @selected(request('category') == 'vegetable')>Vegetables</option>
                    <option value="meat" @selected(request('category') == 'meat')>Meat</option>
                    <option value="dairy" @selected(request('category') == 'dairy')>Dairy</option>
                    <option value="spice" @selected(request('category') == 'spice')>Spices</option>
                    <option value="grain" @selected(request('category') == 'grain')>Grains</option>
                    <option value="beverage" @selected(request('category') == 'beverage')>Beverages</option>
                    <option value="other" @selected(request('category') == 'other')>Other</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
            <div class="flex items-end">
                <a href="{{ route('admin.inventory.lowStock') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Low Stock Items Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lowStockItems as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                        <i class="fas fa-exclamation text-red-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->item_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $item->item_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($item->category) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-red-600 font-bold">{{ number_format($item->current_quantity, 3) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            {{ number_format($item->minimum_quantity, 3) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            {{ $item->unit }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->unit_cost, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->current_quantity <= 0)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Out of Stock
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Low Stock
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.inventory.show', $item) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.inventory.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.inventory.restock', $item) }}" method="POST" class="inline">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="text-green-600 hover:text-green-900" title="Restock">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>
                                <p class="text-lg font-medium">No low stock items found</p>
                                <p class="text-sm">All inventory items are well stocked</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($lowStockItems->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $lowStockItems->links('vendor.pagination.tailwind') }}
        </div>
        @endif
    </div>
</div>
@endsection


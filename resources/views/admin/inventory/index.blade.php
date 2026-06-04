@extends('layouts.admin')

@section('title', 'Inventory')
@section('header', 'Inventory Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex gap-2">
            <a href="{{ route('admin.inventory.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-plus mr-2"></i>Add Item
            </a>
            <a href="{{ route('admin.inventory.lowStock') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">
                <i class="fas fa-exclamation-triangle mr-2"></i>Low Stock
            </a>
        </div>

        <form class="flex gap-2">
            <input type="text" name="search" placeholder="Search items..."
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                value="{{ request('search') }}">
            <button type="submit" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
            <p class="text-sm text-gray-500">Total Items</p>
        </div>
        <div class="bg-green-50 rounded-lg shadow-sm p-4 border border-green-100 text-center">
            <p class="text-2xl font-bold text-green-800">{{ $stats['in_stock'] ?? 0 }}</p>
            <p class="text-sm text-green-600">In Stock</p>
        </div>
        <div class="bg-orange-50 rounded-lg shadow-sm p-4 border border-orange-100 text-center">
            <p class="text-2xl font-bold text-orange-800">{{ $stats['low_stock'] ?? 0 }}</p>
            <p class="text-sm text-orange-600">Low Stock</p>
        </div>
        <div class="bg-red-50 rounded-lg shadow-sm p-4 border border-red-100 text-center">
            <p class="text-2xl font-bold text-red-800">{{ $stats['out_of_stock'] ?? 0 }}</p>
            <p class="text-sm text-red-600">Out of Stock</p>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min. Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($inventory as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-800">{{ $item->name }}</p>
                            <p class="text-sm text-gray-500">{{ $item->sku }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                            {{ $item->category }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-medium text-gray-800">{{ $item->current_quantity }}</span>
                            <span class="text-gray-500">{{ $item->unit }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                            {{ $item->minimum_quantity }} {{ $item->unit }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-800">
                            {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->unit_cost, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->current_quantity <= 0)
                                <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">Out of Stock</span>
                            @elseif($item->current_quantity <= $item->minimum_quantity)
                                <span class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded-full">Low Stock</span>
                            @else
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">In Stock</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button onclick="openRestockModal({{ $item->id }}, '{{ $item->name }}', {{ $item->current_quantity }})"
                                class="text-green-600 hover:text-green-900 mr-3" title="Restock">
                                <i class="fas fa-plus-circle"></i>
                            </button>
                            <a href="{{ route('admin.inventory.edit', $item) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.inventory.destroy', $item) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-boxes text-4xl mb-4 text-gray-300"></i>
                            <p>No inventory items found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $inventory->links() }}
        </div>
    </div>
</div>

<!-- Restock Modal -->
<div id="restockModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" x-show="showModal">
    <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4" @click.away="showModal = false">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Restock Item</h3>
        <form id="restockForm" method="POST">
            @csrf
            <input type="hidden" name="item_id" id="restockItemId">
            <div class="mb-4">
                <p class="text-gray-600">Restocking: <strong id="restockItemName"></strong></p>
                <p class="text-sm text-gray-500">Current: <span id="restockCurrentStock"></span></p>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">Quantity to Add</label>
                <input type="number" name="quantity" min="1" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-600 mb-1">Notes (optional)</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeRestockModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-plus mr-2"></i>Restock
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openRestockModal(id, name, current) {
    document.getElementById('restockModal').classList.remove('hidden');
    document.getElementById('restockModal').classList.add('flex');
    document.getElementById('restockItemId').value = id;
    document.getElementById('restockItemName').textContent = name;
    document.getElementById('restockCurrentStock').textContent = current;
    document.getElementById('restockForm').action = `/admin/inventory/${id}/restock`;
}

function closeRestockModal() {
    document.getElementById('restockModal').classList.add('hidden');
    document.getElementById('restockModal').classList.remove('flex');
}
</script>
@endsection


@extends('layouts.admin')

@section('title', 'Promotions')
@section('header', 'Promotion Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex gap-2">
            <a href="{{ route('admin.promotions.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-plus mr-2"></i>Create Promotion
            </a>
        </div>

        <form class="flex gap-2">
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
            </select>
            <select name="type" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                <option value="">All Types</option>
                <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                <option value="buy_x_get_y" {{ request('type') == 'buy_x_get_y' ? 'selected' : '' }}>Buy X Get Y</option>
                <option value="bogo" {{ request('type') == 'bogo' ? 'selected' : '' }}>BOGO</option>
            </select>
        </form>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] ?? 0 }}</p>
            <p class="text-sm text-gray-500">Total Promotions</p>
        </div>
        <div class="bg-green-50 rounded-lg shadow-sm p-4 border border-green-100 text-center">
            <p class="text-2xl font-bold text-green-800">{{ $stats['active'] ?? 0 }}</p>
            <p class="text-sm text-green-600">Active</p>
        </div>
        <div class="bg-blue-50 rounded-lg shadow-sm p-4 border border-blue-100 text-center">
            <p class="text-2xl font-bold text-blue-800">{{ $stats['upcoming'] ?? 0 }}</p>
            <p class="text-sm text-blue-600">Upcoming</p>
        </div>
        <div class="bg-gray-100 rounded-lg shadow-sm p-4 border border-gray-200 text-center">
            <p class="text-2xl font-bold text-gray-800">{{ $stats['expired'] ?? 0 }}</p>
            <p class="text-sm text-gray-600">Expired</p>
        </div>
    </div>

    <!-- Promotions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($promotions as $promotion)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">{{ $promotion->name }}</h3>
                    <button onclick="toggleStatus({{ $promotion->id }})"
                        class="relative" title="Toggle Status">
                        <div class="w-10 h-5 rounded-full transition-colors duration-300
                            {{ $promotion->is_active ? 'bg-green-500' : 'bg-gray-300' }}">
                            <div class="absolute top-0.5 w-4 h-4 bg-white rounded-full transition-transform duration-300 shadow
                                {{ $promotion->is_active ? 'translate-x-5.5' : 'translate-x-0.5' }}">
                            </div>
                        </div>
                    </button>
                </div>

                <div class="p-4">
                    @if($promotion->description)
                        <p class="text-sm text-gray-600 mb-3">{{ $promotion->description }}</p>
                    @endif

                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-3 py-1 text-lg font-bold text-red-600">
                            @switch($promotion->promotion_type)
                                @case('percentage')
                                    {{ $promotion->discount_value }}% OFF
                                    @break
                                @case('fixed')
                                    {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($promotion->discount_value, 2) }} OFF
                                    @break
                                @case('buy_x_get_y')
                                    Buy {{ $promotion->buy_quantity }} Get {{ $promotion->get_quantity }} Free
                                    @break
                                @case('bogo')
                                    Buy 1 Get 1 FREE
                                    @break
                            @endswitch
                        </span>
                    </div>

                    <div class="text-sm text-gray-500 space-y-1">
                        <p><i class="fas fa-calendar-alt mr-2"></i>{{ \Carbon\Carbon::parse($promotion->start_date)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($promotion->end_date)->format('M d, Y') }}</p>
                        @if($promotion->min_order_amount)
                            <p><i class="fas fa-dollar-sign mr-2"></i>Min. order: {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($promotion->min_order_amount, 2) }}</p>
                        @endif
                        @if($promotion->usage_limit)
                            <p><i class="fas fa-users mr-2"></i>Limit: {{ $promotion->usage_limit }} uses</p>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 mt-4 pt-3 border-t border-gray-100">
                        <a href="{{ route('admin.promotions.show', $promotion) }}" class="flex-1 px-3 py-2 text-center text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        <a href="{{ route('admin.promotions.edit', $promotion) }}" class="flex-1 px-3 py-2 text-center text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <form action="{{ route('admin.promotions.destroy', $promotion) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-3 py-2 text-center text-sm bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <i class="fas fa-tag text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No promotions found</h3>
                    <p class="text-gray-500 mb-4">Get started by creating your first promotion</p>
                    <a href="{{ route('admin.promotions.create') }}" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-plus mr-2"></i>Create Promotion
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="px-6">
        {{ $promotions->links() }}
    </div>
</div>

<script>
function toggleStatus(promotionId) {
    fetch(`/admin/promotions/${promotionId}/toggle`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
@endsection


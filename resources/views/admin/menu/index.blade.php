@extends('layouts.admin')

@section('title', 'Menu Management')
@section('header', 'Menu Management')

@section('content')
<style>
    .admin-category-btn {
        border: 2px solid rgba(231, 215, 200, 0.9);
        background: #fff7ee;
        color: #1f2937;
        transition: all 0.25s ease;
        box-shadow: 0 8px 20px rgba(148, 163, 184, 0.15);
    }
    .admin-category-btn:hover {
        transform: translateY(-1px);
        border-color: rgba(220, 38, 38, 0.4);
        color: #b91c1c;
    }
    .admin-category-btn.is-active {
        background: linear-gradient(135deg, #DC2626 0%, #FBBF24 100%);
        color: #fff;
        border-color: transparent;
        transform: scale(1.03);
        box-shadow: 0 12px 24px rgba(220, 38, 38, 0.28);
    }
</style>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex gap-2">
            <a href="{{ route('admin.menu.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-plus mr-2"></i>Add Item
            </a>
            <a href="{{ route('menu') }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-eye mr-2"></i>Preview
            </a>
        </div>

        <form class="flex gap-2">
            <input type="text" name="search" placeholder="Search menu items..."
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                value="{{ request('search') }}">
            <button type="submit" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- Category Filter -->
    @php
        $normalizeCategory = function ($value) {
            return strtolower(trim(str_replace(['-', '_'], ' ', $value ?? '')));
        };
        $iconMap = [
            'appetizer' => 'fa-pepper-hot',
            'starter' => 'fa-pepper-hot',
            'main course' => 'fa-drumstick-bite',
            'main' => 'fa-drumstick-bite',
            'dessert' => 'fa-ice-cream',
            'sweet' => 'fa-cookie-bite',
            'beverage' => 'fa-glass-whiskey',
            'drink' => 'fa-glass-whiskey',
            'coffee' => 'fa-mug-hot',
            'tea' => 'fa-mug-hot',
            'juice' => 'fa-lemon',
            'smoothie' => 'fa-lemon',
            'special' => 'fa-crown',
            'custom' => 'fa-utensils',
            'vegan' => 'fa-leaf',
            'salad' => 'fa-seedling',
            'pizza' => 'fa-pizza-slice',
            'burger' => 'fa-hamburger',
            'seafood' => 'fa-fish',
            'bread' => 'fa-bread-slice',
        ];
        $resolveIcon = function ($slug, $name) use ($normalizeCategory, $iconMap) {
            $lookup = $normalizeCategory($slug ?: $name);
            if (!$lookup) {
                return 'fa-utensils';
            }
            if (isset($iconMap[$lookup])) {
                return $iconMap[$lookup];
            }
            foreach ($iconMap as $keyword => $icon) {
                if (str_contains($lookup, $keyword)) {
                    return $icon;
                }
            }
            return 'fa-utensils';
        };
    @endphp
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('admin.menu.index') }}"
            class="admin-category-btn px-6 py-3 rounded-2xl font-semibold whitespace-nowrap flex items-center gap-2 {{ !request('category') ? 'is-active' : '' }}">
            <i class="fas fa-star"></i> All Items
        </a>
        @foreach($categories ?? [] as $category)
            @php
                $categoryValue = $category->slug ?? $category->name;
                $categoryValue = strtolower(trim(str_replace(['-', ' '], '_', $categoryValue)));
                $icon = $resolveIcon($categoryValue, $category->name);
                $label = $category->name;
            @endphp
            <a href="{{ route('admin.menu.index', ['category' => $categoryValue]) }}"
                class="admin-category-btn px-6 py-3 rounded-2xl font-semibold whitespace-nowrap flex items-center gap-2 {{ request('category') == $categoryValue ? 'is-active' : '' }}">
                <i class="fas {{ $icon }}"></i> {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Menu Items Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($menuItems as $item)
            @php
                $flavorsRaw = $item->flavors;
                if (is_string($flavorsRaw)) {
                    $flavorsRaw = json_decode($flavorsRaw, true);
                }
                $flavorsRaw = is_array($flavorsRaw) ? $flavorsRaw : [];
                $flavorNames = [];
                $isAssocFlavors = array_keys($flavorsRaw) !== range(0, count($flavorsRaw) - 1);
                if ($isAssocFlavors) {
                    $flavorNames = array_keys($flavorsRaw);
                } else {
                    foreach ($flavorsRaw as $flavor) {
                        if (is_array($flavor)) {
                            $flavorNames[] = $flavor['name'] ?? 'Category';
                        } elseif (is_string($flavor)) {
                            $flavorNames[] = $flavor;
                        }
                    }
                }

                $sizesRaw = $item->sizes;
                if (is_string($sizesRaw)) {
                    $sizesRaw = json_decode($sizesRaw, true);
                }
                $sizesRaw = is_array($sizesRaw) ? $sizesRaw : [];
                $sizeNames = [];
                $isAssocSizes = array_keys($sizesRaw) !== range(0, count($sizesRaw) - 1);
                if ($isAssocSizes) {
                    $sizeNames = array_keys($sizesRaw);
                } else {
                    foreach ($sizesRaw as $size) {
                        if (is_array($size)) {
                            $sizeNames[] = $size['name'] ?? 'Size';
                        } elseif (is_string($size)) {
                            $sizeNames[] = $size;
                        }
                    }
                }
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                <div class="relative">
                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-44 object-cover">
                    <div class="absolute top-3 left-3 px-3 py-1 bg-white/90 rounded-full text-xs font-semibold text-gray-800">
                        {{ \Illuminate\Support\Str::title(str_replace('_', ' ', $item->category ?? 'Uncategorized')) }}
                    </div>
                    @if($item->is_fast_moving)
                        <div class="absolute top-3 right-3 px-3 py-1 bg-red-600 text-white rounded-full text-xs font-semibold">Popular</div>
                    @endif
                </div>

                <div class="p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $item->name }}</h3>
                            <p class="text-sm text-gray-500">Prep: {{ $item->preparation_time ?? 'N/A' }} min</p>
                        </div>
                        <span class="text-lg font-bold text-red-600">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->price, 2) }}</span>
                    </div>

                    @if($item->description)
                        <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $item->description }}</p>
                    @endif

                    @if(count($flavorNames) > 0)
                        <div class="mt-3">
                            <p class="text-xs font-semibold text-gray-500 mb-2">Flavor Preview</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach(array_slice($flavorNames, 0, 4) as $flavorName)
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">{{ $flavorName }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(count($sizeNames) > 0)
                        <div class="mt-3">
                            <p class="text-xs font-semibold text-gray-500 mb-2">Size Preview</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach(array_slice($sizeNames, 0, 4) as $sizeName)
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-full">{{ $sizeName }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center gap-2 mt-3">
                        @if($item->is_available)
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Available</span>
                        @else
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-full">Unavailable</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 mt-4 pt-3 border-t border-gray-100">
                        <a href="{{ route('admin.menu.show', $item) }}" class="flex-1 px-3 py-2 text-center text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            <i class="fas fa-eye mr-1"></i>View
                        </a>
                        <a href="{{ route('admin.menu.edit', $item) }}" class="flex-1 px-3 py-2 text-center text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                        <form action="{{ route('admin.menu.destroy', $item) }}" method="POST" class="flex-1">
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
                    <i class="fas fa-utensils text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No menu items found</h3>
                    <p class="text-gray-500 mb-4">Get started by adding your first menu item</p>
                    <a href="{{ route('admin.menu.create') }}" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-plus mr-2"></i>Add Menu Item
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="px-6">
        {{ $menuItems->links() }}
    </div>
</div>
@endsection


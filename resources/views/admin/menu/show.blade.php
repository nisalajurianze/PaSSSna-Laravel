@extends('layouts.admin')

@section('title', $item->name . ' - Menu Item Details')

@section('styles')
<style>
    .price-badge {
        font-size: 1.25rem;
        font-weight: bold;
        padding: 8px 16px;
        border-radius: 8px;
    }
    .price-badge.original {
        background-color: #f3f4f6;
        color: #6b7280;
    }
    .price-badge.offer {
        background: linear-gradient(135deg, #dc2626, #fbbf24);
        color: white;
    }
    .flavor-chip {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 20px;
        margin: 4px;
        font-size: 14px;
        font-weight: 500;
    }
    .size-badge {
        display: inline-block;
        padding: 8px 16px;
        background-color: #e0f2fe;
        color: #0369a1;
        border-radius: 8px;
        margin: 4px;
        font-weight: 500;
    }
    .topping-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        background-color: #fef3c7;
        color: #92400e;
        border-radius: 20px;
        margin: 4px;
        font-size: 14px;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
    }
    .status-badge.available {
        background-color: #d1fae5;
        color: #065f46;
    }
    .status-badge.unavailable {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .detail-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .detail-card h3 {
        color: #1f2937;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f3f4f6;
    }
    .nutrition-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 12px;
    }
    .nutrition-item {
        text-align: center;
        padding: 12px;
        background-color: #f9fafb;
        border-radius: 8px;
    }
    .nutrition-value {
        font-size: 20px;
        font-weight: bold;
        color: #dc2626;
    }
    .nutrition-label {
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.menu.index') }}"
                       class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $item->name }}</h1>
                        <div class="flex items-center space-x-3 mt-2">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                {{ $item->category->name ?? 'Uncategorized' }}
                            </span>
                            <span class="status-badge {{ $item->is_available ? 'available' : 'unavailable' }}">
                                {{ $item->is_available ? 'Available' : 'Unavailable' }}
                            </span>
                            @if($item->is_fast_moving)
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-medium">
                                <i class="fas fa-fire mr-1"></i>Fast Moving
                            </span>
                            @endif
                            @if($item->is_recommended)
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                                <i class="fas fa-star mr-1"></i>Recommended
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.menu.edit', $item) }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <form action="{{ route('admin.menu.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this item?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-300">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Image Card -->
                <div class="detail-card">
                    <h3><i class="fas fa-image mr-2 text-blue-600"></i>Item Image</h3>
                    <div class="flex justify-center">
                        <div class="w-full max-w-md h-64 rounded-xl overflow-hidden bg-gray-100 flex items-center justify-center">
                            @if($item->image)
                                <img src="{{ $item->image_url }}"
                                     alt="{{ $item->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-utensils text-gray-400 text-6xl"></i>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Description Card -->
                <div class="detail-card">
                    <h3><i class="fas fa-align-left mr-2 text-green-600"></i>Description</h3>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 mb-4">{{ $item->description }}</p>

                        @if($item->short_description)
                        <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-primary-red">
                            <p class="text-gray-600 italic">{{ $item->short_description }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Pricing Card -->
                <div class="detail-card">
                    <h3><i class="fas fa-tag mr-2 text-yellow-600"></i>Pricing</h3>
                    <div class="space-y-4">
                        <!-- Base Price -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-semibold text-gray-800">Base Price</h4>
                                <p class="text-sm text-gray-600">Regular size without any add-ons</p>
                            </div>
                            <div class="price-badge original">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->base_price, 2) }}</div>
                        </div>

                        <!-- Offer Price -->
                        @if($item->offer_price && $item->isOfferActive())
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-red-50 to-yellow-50 rounded-lg border-2 border-dashed border-primary-red">
                            <div>
                                <h4 class="font-semibold text-gray-800">Special Offer Price</h4>
                                <p class="text-sm text-gray-600">
                                    Valid until {{ $item->offer_valid_until->format('M d, Y') }}
                                    @if($item->offer_valid_from)
                                        <br>From {{ $item->offer_valid_from->format('M d, Y') }}
                                    @endif
                                </p>
                            </div>
                            <div class="price-badge offer">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->offer_price, 2) }}</div>
                        </div>
                        <div class="text-center text-sm text-green-600">
                            <i class="fas fa-percentage mr-1"></i>
                            {{ $item->getDiscountPercentage() }}% OFF - Save {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->getDiscountAmount(), 2) }}
                        </div>
                        @endif

                        <!-- Size Prices -->
                        @php
                            $sizes = is_array($item->sizes) ? $item->sizes : [];
                        @endphp
                        @if(count($sizes) > 0)
                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-800 mb-3">Size-Based Pricing</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($sizes as $sizeKey => $sizeData)
                                    @php
                                        $sizeName = is_array($sizeData) ? ($sizeData['name'] ?? $sizeKey) : $sizeKey;
                                        $sizePrice = is_array($sizeData) ? ($sizeData['price'] ?? $item->base_price) : $sizeData;
                                        $sizeDescription = is_array($sizeData) ? ($sizeData['description'] ?? '') : '';
                                        $isDefault = $sizeKey === 'regular';
                                    @endphp
                                    <div class="p-4 border border-gray-200 rounded-lg {{ $isDefault ? 'bg-blue-50 border-blue-200' : '' }}">
                                        <div class="flex justify-between items-start mb-2">
                                            <h5 class="font-semibold {{ $isDefault ? 'text-blue-700' : 'text-gray-800' }}">
                                                {{ $sizeName }}
                                                @if($isDefault)
                                                    <span class="text-xs text-blue-600 ml-1">(Default)</span>
                                                @endif
                                            </h5>
                                            <span class="text-lg font-bold {{ $isDefault ? 'text-blue-700' : 'text-gray-900' }}">
                                                {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($sizePrice, 2) }}
                                            </span>
                                        </div>
                                        @if($sizeDescription)
                                            <p class="text-sm text-gray-600">{{ $sizeDescription }}</p>
                                        @endif
                                        @if(!$isDefault)
                                            @php
                                                $multiplier = $sizePrice / ($item->base_price ?: 1);
                                            @endphp
                                            <p class="text-xs text-gray-500 mt-2">{{ number_format($multiplier, 2) }}× Base Price</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Flavors Card -->
                @php
                    $flavors = is_array($item->flavors) ? $item->flavors : [];
                @endphp
                @if(count($flavors) > 0)
                <div class="detail-card">
                    <h3><i class="fas fa-palette mr-2 text-purple-600"></i>Flavor Options</h3>
                    <div class="space-y-4">
                        @foreach($flavors as $flavor)
                            @php
                                $flavorName = is_array($flavor) ? ($flavor['name'] ?? '') : $flavor;
                                $flavorPrice = is_array($flavor) ? ($flavor['price'] ?? 0) : 0;
                                $flavorColor = is_array($flavor) ? ($flavor['color'] ?? '#DC2626') : '#DC2626';
                            @endphp
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-200">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full mr-3 border-2" style="background-color: {{ $flavorColor }}; border-color: {{ $flavorColor }}"></div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ $flavorName }}</h4>
                                        @if($flavorPrice > 0)
                                            <p class="text-sm text-green-600">+ {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($flavorPrice, 2) }} additional</p>
                                        @else
                                            <p class="text-sm text-gray-500">No additional charge</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-lg font-bold text-gray-900">
                                    {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->base_price + $flavorPrice, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Extra Toppings Card -->
                @php
                    $toppings = is_array($item->extra_toppings) ? $item->extra_toppings : [];
                @endphp
                @if(count($toppings) > 0)
                <div class="detail-card">
                    <h3><i class="fas fa-plus-circle mr-2 text-orange-600"></i>Extra Toppings</h3>
                    <div class="space-y-3">
                        @foreach($toppings as $toppingKey => $topping)
                            @php
                                $toppingName = is_array($topping) ? ($topping['name'] ?? $toppingKey) : $toppingKey;
                                $toppingPrice = is_array($topping) ? ($topping['price'] ?? 0) : $topping;
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                <span class="font-medium text-gray-800">{{ $toppingName }}</span>
                                <span class="font-bold text-orange-700">+ {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($toppingPrice, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Quick Stats Card -->
                <div class="detail-card">
                    <h3><i class="fas fa-chart-bar mr-2 text-primary-red"></i>Quick Stats</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Preparation Time</span>
                            <span class="font-semibold text-gray-900">{{ $item->preparation_time }} minutes</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Food Type</span>
                            <span class="font-semibold capitalize text-gray-900">{{ str_replace('_', ' ', $item->food_type) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Total Orders</span>
                            <span class="font-semibold text-gray-900">{{ $item->total_orders }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Average Rating</span>
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span class="font-semibold text-gray-900">{{ number_format($item->average_rating, 1) }}</span>
                                <span class="text-sm text-gray-500 ml-1">({{ $item->rating_count }})</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Sort Order</span>
                            <span class="font-semibold text-gray-900">{{ $item->sort_order }}</span>
                        </div>
                    </div>
                </div>

                <!-- Order Limits Card -->
                <div class="detail-card">
                    <h3><i class="fas fa-shopping-cart mr-2 text-green-600"></i>Order Limits</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <span class="text-gray-700">Minimum Quantity</span>
                            <span class="font-bold text-green-700">{{ $item->min_order_qty }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <span class="text-gray-700">Maximum Quantity</span>
                            <span class="font-bold text-red-700">{{ $item->max_order_qty }}</span>
                        </div>
                    </div>
                </div>

                <!-- Customization Card -->
                <div class="detail-card">
                    <h3><i class="fas fa-sliders-h mr-2 text-indigo-600"></i>Customization</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">Customizable Item</span>
                            <span class="font-semibold {{ $item->is_customizable ? 'text-green-600' : 'text-red-600' }}">
                                {{ $item->is_customizable ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        @if($item->is_customizable)
                        <div class="mt-3 p-3 bg-indigo-50 rounded-lg">
                            <p class="text-sm text-indigo-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                Customers can customize this item with different ingredients
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Ingredients Card -->
                @php
                    $ingredients = is_array($item->ingredients) ? $item->ingredients : [];
                @endphp
                @if(count($ingredients) > 0)
                <div class="detail-card">
                    <h3><i class="fas fa-utensils mr-2 text-red-600"></i>Ingredients</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($ingredients as $ingredient)
                            <span class="px-3 py-2 bg-red-100 text-red-800 rounded-full text-sm">{{ $ingredient }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Nutrition Card -->
                @php
                    $nutrition = is_array($item->nutrition_info) ? $item->nutrition_info : [];
                @endphp
                @if(count($nutrition) > 0)
                <div class="detail-card">
                    <h3><i class="fas fa-apple-alt mr-2 text-green-600"></i>Nutrition (per serving)</h3>
                    <div class="nutrition-grid">
                        @if(isset($nutrition['calories']))
                        <div class="nutrition-item">
                            <div class="nutrition-value">{{ $nutrition['calories'] }}</div>
                            <div class="nutrition-label">Calories</div>
                        </div>
                        @endif
                        @if(isset($nutrition['protein']))
                        <div class="nutrition-item">
                            <div class="nutrition-value">{{ $nutrition['protein'] }}g</div>
                            <div class="nutrition-label">Protein</div>
                        </div>
                        @endif
                        @if(isset($nutrition['carbs']))
                        <div class="nutrition-item">
                            <div class="nutrition-value">{{ $nutrition['carbs'] }}g</div>
                            <div class="nutrition-label">Carbs</div>
                        </div>
                        @endif
                        @if(isset($nutrition['fat']))
                        <div class="nutrition-item">
                            <div class="nutrition-value">{{ $nutrition['fat'] }}g</div>
                            <div class="nutrition-label">Fat</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="detail-card">
                    <h3><i class="fas fa-cogs mr-2 text-gray-600"></i>Quick Actions</h3>
                    <div class="space-y-3">
                        <form action="{{ route('admin.menu.toggleAvailability', $item) }}" method="POST" class="inline w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full px-4 py-3 text-center rounded-lg font-medium transition duration-300 {{ $item->is_available ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white' }}">
                                <i class="fas {{ $item->is_available ? 'fa-toggle-off' : 'fa-toggle-on' }} mr-2"></i>
                                {{ $item->is_available ? 'Make Unavailable' : 'Make Available' }}
                            </button>
                        </form>

                        <a href="{{ route('admin.menu.edit', $item) }}"
                           class="block w-full px-4 py-3 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition duration-300 font-medium">
                            <i class="fas fa-edit mr-2"></i>Edit Item
                        </a>

                        <a href="{{ route('admin.menu.index') }}"
                           class="block w-full px-4 py-3 bg-gray-200 text-gray-700 text-center rounded-lg hover:bg-gray-300 transition duration-300 font-medium">
                            <i class="fas fa-list mr-2"></i>Back to Menu
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


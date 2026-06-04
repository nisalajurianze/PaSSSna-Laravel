@extends('layouts.admin')

@section('title', 'Customer Menu Preview')
@section('header', 'Customer Menu Preview')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div class="flex gap-2">
            <a href="{{ route('admin.menu.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                <i class="fas fa-arrow-left mr-2"></i>Back to Menu
            </a>
        </div>
        <div class="flex gap-2">
            <span class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm">
                <i class="fas fa-eye mr-2"></i>Preview Mode
            </span>
        </div>
    </div>

    @if($categories->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
            <p class="text-gray-500">No menu items available.</p>
            <a href="{{ route('admin.menu.create') }}" class="mt-4 inline-block px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Add Menu Items
            </a>
        </div>
    @else
        @foreach($categories as $category => $items)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-utensils mr-2"></i>
                    {{ ucfirst(str_replace(['_', '-'], ' ', $category)) }}
                </h2>
            </div>

            <div class="p-6">
                @if($items->isEmpty())
                    <p class="text-gray-500 text-center py-4">No items in this category</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($items as $item)
                        <div class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            @if($item->image)
                                <div class="relative h-48">
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                    @if($item->is_fast_moving)
                                        <span class="absolute top-2 right-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">
                                            <i class="fas fa-fire mr-1"></i>Popular
                                        </span>
                                    @endif
                                    @if($item->is_recommended)
                                        <span class="absolute top-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                                            <i class="fas fa-star mr-1"></i>Recommended
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="h-32 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                    <i class="fas fa-hamburger text-4xl text-gray-400"></i>
                                </div>
                            @endif

                            <div class="p-4">
                                <div class="flex items-start justify-between mb-2">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $item->name }}</h3>
                                    @if($item->food_type)
                                        @if($item->food_type == 'vegetarian')
                                            <span class="text-green-600"><i class="fas fa-leaf"></i></span>
                                        @elseif($item->food_type == 'vegan')
                                            <span class="text-green-500"><i class="fas fa-seedling"></i></span>
                                        @else
                                            <span class="text-red-600"><i class="fas fa-drumstick-bite"></i></span>
                                        @endif
                                    @endif
                                </div>

                                @if($item->short_description)
                                    <p class="text-gray-600 text-sm mb-3">{{ $item->short_description }}</p>
                                @endif

                                @if(is_array($item->sizes) && count($item->sizes) > 0)
                                    <div class="mb-3">
                                        <p class="text-xs font-medium text-gray-500 mb-1">Available Sizes:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($item->sizes as $sizeName => $sizePrice)
                                                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                                    {{ $sizeName }} - {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($sizePrice, 2) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(is_array($item->flavors) && count($item->flavors) > 0)
                                    <div class="mb-3">
                                        <p class="text-xs font-medium text-gray-500 mb-1">Flavors:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($item->flavors as $flavor)
                                                @if(is_array($flavor))
                                                    <span class="text-xs px-2 py-1 rounded" style="background-color: {{ $flavor['color'] ?? '#DC2626' }}20; color: {{ $flavor['color'] ?? '#DC2626' }}">
                                                        {{ $flavor['name'] ?? '' }}
                                                    </span>
                                                @else
                                                    <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ $flavor }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if(is_array($item->extra_toppings) && count($item->extra_toppings) > 0)
                                    <div class="mb-3">
                                        <p class="text-xs font-medium text-gray-500 mb-1">Extra Toppings:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($item->extra_toppings as $toppingName => $toppingPrice)
                                                <span class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded">
                                                    {{ $toppingName }} +{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($toppingPrice, 2) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                                    <div>
                                        @if($item->offer_price)
                                            <span class="text-lg font-bold text-red-600">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->offer_price, 2) }}</span>
                                            <span class="text-sm text-gray-400 line-through ml-2">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->price, 2) }}</span>
                                        @else
                                            <span class="text-lg font-bold text-gray-800">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->price, 2) }}</span>
                                        @endif
                                    </div>
                                    @if($item->preparation_time)
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-clock mr-1"></i>{{ $item->preparation_time }} min
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    @endif

    <div class="flex justify-between items-center mt-6">
        <div class="text-sm text-gray-500">
            Showing {{ $menuItems->count() }} available menu items
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.menu.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                Back to Admin
            </a>
        </div>
    </div>
</div>
@endsection


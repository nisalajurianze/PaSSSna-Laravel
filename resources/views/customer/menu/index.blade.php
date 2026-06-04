@extends('layouts.app')

@section('title', 'Menu - PaSSSna Restaurant')

@section('styles')
<style>
    .category-btn.active {
        background: linear-gradient(135deg, #DC2626 0%, #FBBF24 100%);
        color: white;
        transform: scale(1.05);
    }
    .category-btn {
        transition: all 0.3s ease;
    }
    .menu-card {
        opacity: 0;
        transform: translateY(20px);
        animation: slideUp 0.5s ease forwards;
    }
    @keyframes slideUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .search-highlight {
        background-color: #FEF3C7;
        padding: 0 2px;
        border-radius: 2px;
    }
    .menu-preview-frame {
        position: relative;
        overflow: hidden;
    }
    .menu-preview-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(220,38,38,0.18), rgba(251,191,36,0.18));
        opacity: 0.85;
        mix-blend-mode: screen;
        transition: background 0.4s ease, opacity 0.4s ease;
        pointer-events: none;
    }
    .menu-preview-image {
        transition: transform 0.6s ease, filter 0.6s ease, opacity 0.35s ease;
        will-change: transform, filter, opacity;
    }
    .menu-card.is-animating .menu-preview-image {
        transform: scale(1.05);
        filter: saturate(1.1) brightness(1.05);
    }
    .menu-preview-chip {
        transition: all 0.25s ease;
    }
    .menu-preview-chip.active {
        border-color: #DC2626;
        background: rgba(220, 38, 38, 0.08);
        color: #991B1B;
    }
</style>
@endsection

@section('content')
<div class="bg-gradient-to-b from-gray-50 to-white">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-primary-red/10 via-primary-yellow/10 to-primary-red/10 py-16">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">Our Menu</h1>
                <p class="text-xl text-gray-600 mb-8">Discover our carefully crafted dishes made with passion and premium ingredients</p>

                <!-- Search Bar -->
                <div class="max-w-2xl mx-auto mb-8">
                    <form action="{{ route('menu') }}" method="GET" class="relative" id="searchForm">
                        <div class="relative">
                            <input type="text"
                                   id="menuSearch"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search for dishes, ingredients, or categories..."
                                   autocomplete="off"
                                   class="w-full px-6 py-4 pl-14 text-lg rounded-2xl border-2 border-gray-200 focus:border-primary-red focus:ring-2 focus:ring-primary-red/20 transition-all duration-300">
                            <div class="absolute left-5 top-1/2 transform -translate-y-1/2">
                                <i class="fas fa-search text-gray-400 text-xl"></i>
                            </div>
                            <button type="submit"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-primary-red text-white px-6 py-2 rounded-xl hover:bg-red-700 transition duration-300">
                                Search
                            </button>
                        </div>
                        <!-- Search Results Dropdown -->
                        <div id="searchResults" class="absolute z-50 left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-gray-100 hidden max-h-96 overflow-y-auto"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Content -->
    <div class="container mx-auto px-4 py-12">
        <!-- Categories Filter -->
        <div class="mb-12">
            <div class="flex flex-wrap gap-3 justify-center">
                <button onclick="filterCategory('all')"
                        class="category-btn px-6 py-3 bg-white border-2 border-gray-200 rounded-xl font-medium hover:border-primary-red hover:text-primary-red {{ !request('category') || request('category') == 'all' ? 'active' : '' }}">
                    <i class="fas fa-star mr-2"></i>All Items
                </button>

                <button onclick="filterCategory('appetizer')"
                        class="category-btn px-6 py-3 bg-white border-2 border-gray-200 rounded-xl font-medium hover:border-primary-red hover:text-primary-red {{ request('category') == 'appetizer' ? 'active' : '' }}">
                    <i class="fas fa-pepper-hot mr-2"></i>Appetizers
                </button>

                <button onclick="filterCategory('main')"
                        class="category-btn px-6 py-3 bg-white border-2 border-gray-200 rounded-xl font-medium hover:border-primary-red hover:text-primary-red {{ request('category') == 'main' ? 'active' : '' }}">
                    <i class="fas fa-drumstick-bite mr-2"></i>Main Course
                </button>

                <button onclick="filterCategory('dessert')"
                        class="category-btn px-6 py-3 bg-white border-2 border-gray-200 rounded-xl font-medium hover:border-primary-red hover:text-primary-red {{ request('category') == 'dessert' ? 'active' : '' }}">
                    <i class="fas fa-ice-cream mr-2"></i>Desserts
                </button>

                <button onclick="filterCategory('drink')"
                        class="category-btn px-6 py-3 bg-white border-2 border-gray-200 rounded-xl font-medium hover:border-primary-red hover:text-primary-red {{ request('category') == 'drink' ? 'active' : '' }}">
                    <i class="fas fa-glass-whiskey mr-2"></i>Drinks
                </button>

                <button onclick="filterCategory('special')"
                        class="category-btn px-6 py-3 bg-white border-2 border-gray-200 rounded-xl font-medium hover:border-primary-red hover:text-primary-red {{ request('category') == 'special' ? 'active' : '' }}">
                    <i class="fas fa-crown mr-2"></i>Specials
                </button>
            </div>
        </div>

        <!-- Results Count -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <p class="text-gray-600">
                    Showing
                    <span class="font-bold text-primary-red">{{ $menuItems->firstItem() ?? 0 }}-{{ $menuItems->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-bold text-primary-red">{{ $menuItems->total() }}</span>
                    items
                    @if(request('search'))
                        for "<span class="font-semibold">{{ request('search') }}</span>"
                    @endif
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Sort by:</span>
                <select onchange="sortItems(this.value)" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-red focus:border-transparent">
                    <option value="popular">Most Popular</option>
                    <option value="price_low">Price: Low to High</option>
                    <option value="price_high">Price: High to Low</option>
                    <option value="newest">Newest First</option>
                </select>
            </div>
        </div>

        <!-- Menu Grid -->
        @if($menuItems->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($menuItems as $index => $item)
                @php
                    $flavors = $item->flavors;
                    if (is_string($flavors)) {
                        $flavors = json_decode($flavors, true);
                    }
                    $flavors = is_array($flavors) ? $flavors : [];
                    $flavorList = [];
                    $isAssocFlavors = array_keys($flavors) !== range(0, count($flavors) - 1);
                    if ($isAssocFlavors) {
                        foreach ($flavors as $name => $price) {
                            $flavorList[] = [
                                'name' => (string) $name,
                                'price' => (float) $price,
                                'image' => null,
                                'color' => '#DC2626',
                            ];
                        }
                    } else {
                        $flavorList = collect($flavors)->map(function ($flavor) {
                            if (is_array($flavor)) {
                                $image = $flavor['image'] ?? null;
                                if ($image && !Str::startsWith($image, ['http://', 'https://', 'data:', '/'])) {
                                    $image = asset('storage/' . $image);
                                }
                                return [
                                    'name' => $flavor['name'] ?? 'Category',
                                    'price' => (float) ($flavor['price'] ?? 0),
                                    'image' => $image,
                                    'color' => $flavor['color'] ?? '#DC2626',
                                ];
                            }
                            return [
                                'name' => (string) $flavor,
                                'price' => 0,
                                'image' => null,
                                'color' => '#DC2626',
                            ];
                        })->values()->all();
                    }

                    $sizes = $item->sizes;
                    if (is_string($sizes)) {
                        $sizes = json_decode($sizes, true);
                    }
                    $sizes = is_array($sizes) ? $sizes : [];
                    $isAssocSizes = array_keys($sizes) !== range(0, count($sizes) - 1);
                    if (!$isAssocSizes) {
                        $normalizedSizes = [];
                        foreach ($sizes as $size) {
                            if (is_array($size)) {
                                $sizeName = $size['name'] ?? null;
                                if (!$sizeName) {
                                    continue;
                                }
                                $sizePrice = $size['price'] ?? ($item->price + ($size['price_modifier'] ?? 0));
                                $normalizedSizes[$sizeName] = (float) $sizePrice;
                            } elseif (is_string($size)) {
                                $normalizedSizes[$size] = (float) $item->price;
                            }
                        }
                        $sizes = $normalizedSizes;
                    }

                    $variantImages = [];
                    if (is_array($item->nutrition_info ?? null)) {
                        $variantImages = $item->nutrition_info['variant_images'] ?? [];
                    }
                    if (is_string($variantImages)) {
                        $variantImages = json_decode($variantImages, true);
                    }
                    $variantImages = is_array($variantImages) ? $variantImages : [];
                    $variantImages = collect($variantImages)->map(function ($variant) {
                        if (!is_array($variant)) {
                            return null;
                        }
                        $image = $variant['image'] ?? null;
                        if ($image && !Str::startsWith($image, ['http://', 'https://', 'data:', '/'])) {
                            $image = asset('storage/' . $image);
                        }
                        return [
                            'flavor' => $variant['flavor'] ?? null,
                            'size' => $variant['size'] ?? null,
                            'image' => $image,
                        ];
                    })->filter()->values()->all();

                    $previewImage = $item->image_url;
                @endphp
                <div class="menu-card bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer"
                     onclick="window.location.href='{{ route('menu.show', $item->id) }}'"
                     style="animation-delay: {{ $index * 0.05 }}s">
                    <!-- Image -->
                    <div class="menu-preview-frame relative overflow-hidden h-56"
                         data-flavors='@json($flavorList)'
                         data-sizes='@json($sizes)'
                         data-variants='@json($variantImages)'
                         data-base-image="{{ $previewImage }}">
                        <div class="menu-preview-overlay"></div>
                        <img src="{{ $previewImage }}"
                             alt="{{ $item->name }}"
                             class="menu-preview-image w-full h-full object-cover transition-transform duration-500 hover:scale-110">

                        <!-- Category Badge -->
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-sm font-semibold text-gray-800">
                                {{ ucfirst($item->category) }}
                            </span>
                        </div>

                        <!-- Quick Actions -->
                        <div class="absolute top-4 right-4 flex flex-col gap-2">
                            @if($item->is_fast_moving)
                            <span class="px-3 py-1 bg-primary-red text-white rounded-full text-sm font-semibold">
                                <i class="fas fa-fire mr-1"></i>Popular
                            </span>
                            @endif

                            @if($item->offer_price && $item->offer_valid_until > now())
                            <span class="px-3 py-1 bg-primary-yellow text-gray-800 rounded-full text-sm font-semibold">
                                <i class="fas fa-tag mr-1"></i>Offer
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-xl font-bold text-gray-900">
                                @if(request('search'))
                                    {!! highlightText($item->name, request('search')) !!}
                                @else
                                    {{ $item->name }}
                                @endif
                            </h3>
                            <div class="text-right">
                                @if($item->offer_price && $item->offer_valid_until > now())
                                    <div class="text-2xl font-bold text-primary-red">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->offer_price, 2) }}</div>
                                    <div class="text-gray-400 line-through text-sm">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->price, 2) }}</div>
                                @else
                                    <div class="text-2xl font-bold text-gray-900">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->price, 2) }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="text-gray-600 mb-4">
                            @if(request('search'))
                                {!! highlightText(Str::limit($item->description, 120), request('search')) !!}
                            @else
                                {{ Str::limit($item->description, 120) }}
                            @endif
                        </p>

                        <!-- Flavors -->
                        @if(count($flavorList) > 0)
                        <div class="mb-4">
                            <div class="text-sm font-medium text-gray-700 mb-2">Quick Category Preview</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach(array_slice($flavorList, 0, 4) as $flavor)
                                <button type="button"
                                        class="menu-preview-chip px-3 py-1 border border-gray-200 bg-gray-50 text-gray-700 rounded-full text-sm"
                                        data-flavor="{{ $flavor['name'] }}"
                                        data-image="{{ $flavor['image'] }}"
                                        data-color="{{ $flavor['color'] }}"
                                        onclick="event.stopPropagation();">
                                    {{ $flavor['name'] }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if(count($sizes) > 0)
                        <div class="mb-4">
                            <div class="text-sm font-medium text-gray-700 mb-2">Size Preview</div>
                            <div class="flex flex-wrap gap-2">
                                @foreach(array_slice($sizes, 0, 4) as $size => $price)
                                <button type="button"
                                        class="menu-preview-chip px-3 py-1 border border-gray-200 bg-gray-50 text-gray-700 rounded-full text-sm"
                                        data-size="{{ $size }}"
                                        onclick="event.stopPropagation();">
                                    {{ ucfirst($size) }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="text-xs text-gray-500 mb-4">
                            Preview: <span class="menu-preview-label">Default</span>
                        </div>

                        <!-- Preparation Time & Rating -->
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-clock mr-2"></i>
                                <span>{{ $item->preparation_time }} min</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span class="font-medium">4.5</span>
                                <span class="text-gray-500 text-sm ml-1">(24)</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-between items-center">
                            <a href="{{ route('menu.show', $item->id) }}" onclick="event.stopPropagation();"
                               class="flex-1 mr-2 text-center py-3 border-2 border-primary-red text-primary-red rounded-xl hover:bg-primary-red hover:text-white transition duration-300 font-semibold">
                                <i class="fas fa-eye mr-2"></i>View Details
                            </a>

                            <button onclick="event.stopPropagation(); addToCart({{ $item->id }})"
                                    class="flex-1 ml-2 py-3 bg-gradient-to-r from-primary-red to-red-600 text-white rounded-xl hover:from-red-700 hover:to-red-800 transition duration-300 font-semibold">
                                <i class="fas fa-cart-plus mr-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($menuItems->hasPages())
            <div class="mt-12">
                {{ $menuItems->links('vendor.pagination.tailwind') }}
            </div>
            @endif

        @else
            <!-- No Results -->
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-700 mb-3">No items found</h3>
                <p class="text-gray-600 mb-8">Try adjusting your search or filter to find what you're looking for.</p>
                <a href="{{ route('menu') }}" class="inline-block bg-primary-red text-white px-8 py-3 rounded-xl hover:bg-red-700 transition duration-300">
                    Clear Filters
                </a>
            </div>
        @endif

        <!-- Fast Moving Section -->
        @if($fastMoving->count() > 0)
        <div class="mt-20">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">🔥 Fast Moving Items</h2>
                <p class="text-gray-600">Our customers love these dishes</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($fastMoving as $item)
                <div class="bg-white rounded-xl shadow-lg p-6 flex items-center hover:shadow-xl transition duration-300">
                    <div class="w-20 h-20 rounded-lg overflow-hidden mr-4">
                        <img src="{{ $item->image_url }}"
                             alt="{{ $item->name }}"
                             class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">{{ $item->name }}</h4>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-lg font-bold text-primary-red">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->price, 2) }}</span>
                            <button onclick="addToCart({{ $item->id }})"
                                    class="bg-primary-red text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Offers Section -->
        @if($offers->count() > 0)
        <div class="mt-20">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">🎉 Special Offers</h2>
                <p class="text-gray-600">Limited time deals you don't want to miss</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($offers as $offer)
                <div class="bg-gradient-to-r from-primary-red to-primary-yellow p-1 rounded-2xl">
                    <div class="bg-white rounded-2xl p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-gradient-to-r from-primary-red to-primary-yellow rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-percentage text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $offer->name }}</h3>
                                <div class="flex items-center text-gray-600 text-sm">
                                    <i class="fas fa-clock mr-2"></i>
                                    <span>Ends {{ $offer->offer_valid_until->format('M d') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-2xl font-bold text-primary-red">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($offer->offer_price, 2) }}</span>
                                <span class="text-gray-400 line-through ml-2">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($offer->price, 2) }}</span>
                            </div>
                            <a href="{{ route('menu.show', $offer->id) }}"
                               class="bg-primary-red text-white px-6 py-2 rounded-xl hover:bg-red-700 transition duration-300">
                                Grab Offer
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    const currencySymbol = @json(config('restaurant.payment.currency_symbol', 'LKR '));

    function filterCategory(category) {
        const url = new URL(window.location.href);
        url.searchParams.set('category', category);
        window.location.href = url.toString();
    }

    function sortItems(sortBy) {
        const url = new URL(window.location.href);
        url.searchParams.set('sort', sortBy);
        window.location.href = url.toString();
    }

    function addToCart(itemId) {
        fetch('{{ route("cart.add", [], false) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                item_id: itemId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showToast('Added to cart!', 'success');
                updateCartCount(data.cart_count);
            }
        });
    }

    function updateCartCount(count) {
        let cartBadge = document.querySelector('.fa-shopping-cart').parentElement.querySelector('span');
        if(cartBadge) {
            cartBadge.textContent = count;
            cartBadge.classList.add('animate-pulse');
            setTimeout(() => cartBadge.classList.remove('animate-pulse'), 1000);
        } else {
            const cartLink = document.querySelector('.fa-shopping-cart').parentElement;
            cartBadge = document.createElement('span');
            cartBadge.className = 'absolute -top-1 -right-1 bg-primary-red text-white text-xs rounded-full w-5 h-5 flex items-center justify-center animate-pulse';
            cartBadge.textContent = count;
            cartLink.appendChild(cartBadge);
        }
    }

    // Real-time Search Functionality
    let searchTimeout = null;
    const searchInput = document.getElementById('menuSearch');
    const searchResults = document.getElementById('searchResults');

    if (searchInput && searchResults) {
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.trim();

            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            // Hide results if query is empty
            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }

            // Debounce the search
            searchTimeout = setTimeout(() => {
                performSearch(query);
            }, 300);
        });

        // Hide results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });

        // Keep search box focused when clicking on results
        searchResults.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    function performSearch(query) {
        fetch('{{ route('menu.search') }}?q=' + encodeURIComponent(query), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(async response => {
                const contentType = response.headers.get('content-type') || '';
                if (!response.ok) {
                    const body = await response.text();
                    throw new Error(`Search failed (${response.status}): ${body.slice(0, 200)}`);
                }
                if (!contentType.includes('application/json')) {
                    const body = await response.text();
                    throw new Error(`Search returned non-JSON (${contentType || 'unknown'}): ${body.slice(0, 200)}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.items.length > 0) {
                    displaySearchResults(data.items, query);
                } else {
                    searchResults.innerHTML = '<div class="p-4 text-center text-gray-500"><i class="fas fa-search text-3xl mb-2"></i><p>No items found for "' + query + '"</p></div>';
                    searchResults.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Search error:', error);
                searchResults.innerHTML = '<div class="p-4 text-center text-gray-500"><i class="fas fa-exclamation-triangle text-3xl mb-2"></i><p>Search is temporarily unavailable. Please try again.</p></div>';
                searchResults.classList.remove('hidden');
            });
    }

    function displaySearchResults(items, query) {
        let html = '';
        items.forEach(item => {
            const price = item.offer_price ? item.offer_price : item.price;
            const imageUrl = item.image ? '/storage/' + item.image : '{{ asset('images/menu/placeholders/default.svg') }}';

            html += '<a href="/menu/item/' + item.id + '" class="block p-4 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition duration-200">' +
                    '<div class="flex items-center gap-4">' +
                    '<img src="' + imageUrl + '" alt="' + item.name + '" class="w-16 h-16 object-cover rounded-xl">' +
                    '<div class="flex-1">' +
                    '<h4 class="font-semibold text-gray-800">' + highlightMatch(item.name, query) + '</h4>' +
                    '<p class="text-sm text-gray-500 capitalize">' + item.category + '</p>' +
                    '<div class="flex items-center gap-2 mt-1">' +
                    '<span class="font-bold text-primary-red">' + currencySymbol + Number(price).toFixed(2) + '</span>' +
                    (item.offer_price ? '<span class="text-sm text-gray-400 line-through">' + currencySymbol + Number(item.price).toFixed(2) + '</span>' : '') +
                    '</div></div><i class="fas fa-arrow-right text-gray-400"></i></div></a>';
        });

        // Add "View all results" link
        html += '<div class="p-3 bg-gray-50 text-center border-t border-gray-100">' +
                '<a href="{{ route('menu') }}?search=' + encodeURIComponent(query) + '" class="text-primary-red font-medium hover:underline">' +
                'View all results for "' + query + '" <i class="fas fa-arrow-right ml-1"></i></a></div>';

        searchResults.innerHTML = html;
        searchResults.classList.remove('hidden');
    }

    function highlightMatch(text, query) {
        if (!query) return text;
        const regex = new RegExp('(' + escapeRegex(query) + ')', 'gi');
        return text.replace(regex, '<span class="search-highlight">$1</span>');
    }

    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function generateFlavorImage(color, label) {
        const safeLabel = (label || 'Category').replace(/[^a-z0-9 ]/gi, '').trim().slice(0, 16);
        const svg = `
            <svg xmlns="http://www.w3.org/2000/svg" width="800" height="600" viewBox="0 0 800 600">
                <defs>
                    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0" stop-color="${color}55"/>
                        <stop offset="1" stop-color="#FBBF2488"/>
                    </linearGradient>
                </defs>
                <rect width="800" height="600" rx="48" fill="url(#bg)"/>
                <circle cx="680" cy="140" r="90" fill="${color}" opacity="0.25"/>
                <text x="50%" y="52%" text-anchor="middle" font-family="Poppins, Arial, sans-serif" font-size="48" fill="#111827" font-weight="700">${safeLabel}</text>
                <text x="50%" y="60%" text-anchor="middle" font-family="Poppins, Arial, sans-serif" font-size="22" fill="#1F2937">PaSSSna</text>
            </svg>
        `;
        return 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg);
    }

    // Flavor/Size preview on menu cards
    document.addEventListener('DOMContentLoaded', function() {
        const sizeGradients = {
            small: 'linear-gradient(135deg, rgba(59,130,246,0.35), rgba(14,116,144,0.45))',
            regular: 'linear-gradient(135deg, rgba(251,191,36,0.35), rgba(234,88,12,0.45))',
            medium: 'linear-gradient(135deg, rgba(244,63,94,0.35), rgba(190,24,93,0.45))',
            large: 'linear-gradient(135deg, rgba(99,102,241,0.35), rgba(59,130,246,0.45))',
        };

        document.querySelectorAll('.menu-card').forEach(card => {
            const frame = card.querySelector('.menu-preview-frame');
            const image = card.querySelector('.menu-preview-image');
            const overlay = card.querySelector('.menu-preview-overlay');
            const label = card.querySelector('.menu-preview-label');
            if (!frame || !image || !overlay) return;

            const baseImage = frame.dataset.baseImage || image.src;
            const variantData = frame.dataset.variants ? JSON.parse(frame.dataset.variants) : [];
            const state = { flavor: null, size: null };

            const findVariantImage = () => {
                if (!state.flavor || !state.size || !Array.isArray(variantData)) return null;
                const flavorKey = String(state.flavor).toLowerCase();
                const sizeKey = String(state.size).toLowerCase();
                const match = variantData.find(item => {
                    if (!item) return false;
                    const flavor = String(item.flavor || '').toLowerCase();
                    const size = String(item.size || '').toLowerCase();
                    return flavor === flavorKey && size === sizeKey;
                });
                return match ? match.image : null;
            };

            const applyPreview = (name, color, img, preserveOverlay = false) => {
                card.classList.add('is-animating');
                if (label && name) label.textContent = name;
                const overlayColor = color || '#DC2626';
                if (!preserveOverlay) {
                    overlay.style.background = `linear-gradient(135deg, ${overlayColor}55, rgba(251,191,36,0.25))`;
                }

                let resolved = img;
                if (!resolved || (typeof resolved === 'string' && resolved.includes('unsplash.com'))) {
                    if (overlayColor) {
                        resolved = generateFlavorImage(overlayColor, name || 'Category');
                    }
                }

                if (resolved) {
                    image.style.opacity = '0.7';
                    setTimeout(() => {
                        image.src = resolved;
                        image.style.opacity = '1';
                    }, 160);
                } else {
                    image.src = baseImage;
                }

                setTimeout(() => card.classList.remove('is-animating'), 420);
            };

            card.querySelectorAll('.menu-preview-chip[data-flavor]').forEach(chip => {
                chip.addEventListener('click', () => {
                    card.querySelectorAll('.menu-preview-chip[data-flavor]').forEach(c => c.classList.remove('active'));
                    chip.classList.add('active');
                    state.flavor = chip.dataset.flavor;
                    const variantImage = findVariantImage();
                    applyPreview(chip.dataset.flavor, chip.dataset.color, variantImage || chip.dataset.image);
                });
            });

            card.querySelectorAll('.menu-preview-chip[data-size]').forEach(chip => {
                chip.addEventListener('click', () => {
                    card.querySelectorAll('.menu-preview-chip[data-size]').forEach(c => c.classList.remove('active'));
                    chip.classList.add('active');
                    const sizeKey = (chip.dataset.size || '').toLowerCase();
                    const gradient = sizeGradients[sizeKey] || 'linear-gradient(135deg, rgba(220,38,38,0.25), rgba(251,191,36,0.25))';
                    overlay.style.background = gradient;
                    if (label) label.textContent = `${chip.dataset.size} size`;
                    card.classList.add('is-animating');
                    setTimeout(() => card.classList.remove('is-animating'), 360);
                    state.size = chip.dataset.size;
                    const variantImage = findVariantImage();
                    if (variantImage) {
                        applyPreview(label?.textContent || chip.dataset.size, null, variantImage, true);
                    }
                });
            });
        });
    });
</script>

@php
    function highlightText($text, $search) {
        if(!$search) return $text;
        $pattern = '/(' . preg_quote($search, '/') . ')/i';
        return preg_replace($pattern, '<span class="search-highlight">$1</span>', $text);
    }
@endphp
@endsection


@extends('layouts.app')

@section('title', $menu->name . ' - PaSSSna Restaurant')

@section('styles')
<style>
    .flavor-item {
        opacity: 0;
        transform: translateX(-20px);
        transition: all 0.5s ease;
    }
    .flavor-item.visible {
        opacity: 1;
        transform: translateX(0);
    }
    .size-option {
        transition: all 0.3s ease;
    }
    .size-option.active {
        background: linear-gradient(135deg, #DC2626 0%, #FBBF24 100%);
        color: white;
        transform: scale(1.05);
    }
    .topping-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .quantity-btn {
        transition: all 0.2s ease;
    }
    .quantity-btn:hover {
        background-color: #FEE2E2;
    }
    .media-frame {
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.18);
        background: #0f172a;
    }
    .media-frame::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.35), transparent 50%);
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
    }
    .media-frame.is-animating::after {
        opacity: 1;
    }
    .media-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(220,38,38,0.25), rgba(251,191,36,0.25));
        mix-blend-mode: screen;
        opacity: 0.8;
        transition: background 0.45s ease, opacity 0.45s ease;
        pointer-events: none;
    }
    .media-image {
        transition: transform 0.6s ease, filter 0.6s ease, opacity 0.4s ease;
        will-change: transform, filter, opacity;
    }
    .media-frame.is-animating .media-image {
        transform: scale(1.04);
        filter: saturate(1.1) brightness(1.05);
    }
    .flavor-card.active {
        border-color: #DC2626;
        box-shadow: 0 12px 28px rgba(220, 38, 38, 0.18);
        background: linear-gradient(140deg, rgba(220,38,38,0.08), rgba(251,191,36,0.15));
    }
</style>
@endsection

@section('content')
<div class="bg-white">
    @php
        $flavorsRaw = $menu->flavors;
        if (is_string($flavorsRaw)) {
            $flavorsRaw = json_decode($flavorsRaw, true);
        }
        $flavorsRaw = is_array($flavorsRaw) ? $flavorsRaw : [];
        $flavorOptions = [];
        $isAssocFlavors = array_keys($flavorsRaw) !== range(0, count($flavorsRaw) - 1);
        if ($isAssocFlavors) {
            foreach ($flavorsRaw as $name => $price) {
                $flavorOptions[] = [
                    'name' => (string) $name,
                    'price' => (float) $price,
                    'image' => null,
                    'color' => '#DC2626',
                ];
            }
        } else {
            foreach ($flavorsRaw as $flavor) {
                if (is_array($flavor)) {
                    $image = $flavor['image'] ?? null;
                    if ($image && !Str::startsWith($image, ['http://', 'https://', 'data:', '/'])) {
                        $image = asset('storage/' . $image);
                    }
                    $flavorOptions[] = [
                        'name' => $flavor['name'] ?? 'Category',
                        'price' => (float) ($flavor['price'] ?? 0),
                        'image' => $image,
                        'color' => $flavor['color'] ?? '#DC2626',
                    ];
                } else {
                    $flavorOptions[] = [
                        'name' => (string) $flavor,
                        'price' => 0,
                        'image' => null,
                        'color' => '#DC2626',
                    ];
                }
            }
        }
        $sizesRaw = $menu->sizes;
        if (is_string($sizesRaw)) {
            $sizesRaw = json_decode($sizesRaw, true);
        }
        $sizesRaw = is_array($sizesRaw) ? $sizesRaw : [];
        $isAssocSizes = array_keys($sizesRaw) !== range(0, count($sizesRaw) - 1);
        if (!$isAssocSizes) {
            $normalizedSizes = [];
            foreach ($sizesRaw as $size) {
                if (is_array($size)) {
                    $sizeName = $size['name'] ?? null;
                    if (!$sizeName) {
                        continue;
                    }
                    $sizePrice = $size['price'] ?? ($menu->price + ($size['price_modifier'] ?? 0));
                    $normalizedSizes[$sizeName] = (float) $sizePrice;
                } elseif (is_string($size)) {
                    $normalizedSizes[$size] = (float) $menu->price;
                }
            }
            $sizesRaw = $normalizedSizes;
        }
        $sizeGradients = [];
        if (is_array($menu->nutrition_info ?? null) && isset($menu->nutrition_info['size_gradients'])) {
            $sizeGradients = $menu->nutrition_info['size_gradients'];
        }
        $firstSizeKey = $sizesRaw ? array_key_first($sizesRaw) : null;
        $variantImages = [];
        if (is_array($menu->nutrition_info ?? null)) {
            $variantImages = $menu->nutrition_info['variant_images'] ?? [];
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

        $toppingsRaw = $menu->extra_toppings;
        if (is_string($toppingsRaw)) {
            $toppingsRaw = json_decode($toppingsRaw, true);
        }
        $toppingsRaw = is_array($toppingsRaw) ? $toppingsRaw : [];
        $toppingsList = [];
        $isAssocToppings = array_keys($toppingsRaw) !== range(0, count($toppingsRaw) - 1);
        if ($isAssocToppings) {
            $toppingsList = $toppingsRaw;
        } else {
            foreach ($toppingsRaw as $topping) {
                if (is_array($topping)) {
                    $toppingsList[$topping['name'] ?? 'Topping'] = (float) ($topping['price'] ?? 0);
                } elseif (is_string($topping)) {
                    $toppingsList[$topping] = 0;
                }
            }
        }
    @endphp
    <!-- Breadcrumb -->
    <div class="bg-gray-50 py-4">
        <div class="container mx-auto px-4">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}" class="text-gray-600 hover:text-primary-red">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <a href="{{ route('menu') }}" class="text-gray-600 hover:text-primary-red">Menu</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-gray-900 font-medium">{{ $menu->name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Product Details -->
    <div class="container mx-auto px-4 py-12">
        <div class="grid md:grid-cols-2 gap-12">
            <!-- Left Column - Images -->
            <div>
                <!-- Main Image -->
                <div id="imageFrame" class="media-frame mb-6">
                    <div id="imageOverlay" class="media-overlay"></div>
                    <img id="mainImage"
                         src="{{ $menu->image_url }}"
                         alt="{{ $menu->name }}"
                         class="w-full h-96 object-cover media-image">
                </div>

                <!-- Offer Banner -->
                @if($menu->offer_price && $menu->offer_valid_until > now())
                <div class="bg-gradient-to-r from-primary-red to-primary-yellow text-white p-6 rounded-2xl mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-2xl font-bold mb-2">Special Offer!</div>
                            <div class="flex items-center">
                                <i class="fas fa-clock mr-2"></i>
                                <span>Valid until {{ $menu->offer_valid_until->format('F d, Y') }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-4xl font-bold">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($menu->offer_price, 2) }}</div>
                            <div class="text-lg line-through opacity-75">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($menu->price, 2) }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Details -->
            <div>
                <!-- Title & Price -->
                <div class="mb-6">
                    <h1 class="text-4xl font-bold text-gray-900 mb-3">{{ $menu->name }}</h1>
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= 4 ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            @endfor
                            <span class="ml-2 text-gray-600">4.5 (24 reviews)</span>
                        </div>
                        <div class="text-gray-600">
                            <i class="fas fa-clock mr-1"></i>
                            {{ $menu->preparation_time }} min prep time
                        </div>
                    </div>

                    <div class="text-3xl font-bold text-primary-red mb-2">
                        @if($menu->offer_price && $menu->offer_valid_until > now())
                            {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($menu->offer_price, 2) }}
                            <span class="text-xl text-gray-400 line-through ml-2">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($menu->price, 2) }}</span>
                        @else
                            {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($menu->price, 2) }}
                        @endif
                    </div>

                    @if($menu->is_fast_moving)
                    <div class="inline-flex items-center px-3 py-1 bg-red-100 text-primary-red rounded-full text-sm font-semibold mt-2">
                        <i class="fas fa-fire mr-2"></i>Fast Moving Item
                    </div>
                    @endif
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Description</h3>
                    <p class="text-gray-700">{{ $menu->description }}</p>
                </div>

                <!-- Flavors Section -->
                @if($menu->flavors)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Choose Category</h3>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($flavorOptions as $index => $flavor)
                        <button type="button"
                                class="flavor-item flavor-card flavor-{{ $index }} p-4 bg-gray-50 rounded-xl border border-gray-200 text-left"
                                onclick="selectFlavor('{{ addslashes($flavor['name']) }}', {{ $flavor['price'] }}, '{{ $flavor['image'] }}', '{{ $flavor['color'] }}')">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3"
                                     style="background: {{ $flavor['color'] }}22; color: {{ $flavor['color'] }}">
                                    <i class="fas fa-martini-glass-citrus"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $flavor['name'] }}</div>
                                    <div class="text-sm text-gray-600">
                                        @if($flavor['price'] > 0)
                                            + {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($flavor['price'], 2) }}
                                        @else
                                            Signature option
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Sizes -->
                @if($menu->sizes)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Size</h3>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach($sizesRaw as $size => $price)
                        <div class="size-option text-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-primary-red"
                             onclick="selectSize('{{ addslashes($size) }}', {{ $price }}, '{{ $sizeGradients[$size] ?? '' }}')"
                             id="size-{{ Str::slug($size) }}">
                            <div class="font-bold text-gray-900 mb-1">{{ ucfirst($size) }}</div>
                            <div class="text-lg font-bold text-primary-red">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($price, 2) }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Extra Toppings -->
                @if(count($toppingsList) > 0)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Extra Toppings</h3>
                    <div class="space-y-3">
                        @foreach($toppingsList as $topping => $price)
                        <div class="topping-item flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200 hover:bg-white transition duration-300 cursor-pointer"
                             onclick="toggleTopping('{{ $topping }}', {{ $price }})">
                            <div class="flex items-center">
                                <input type="checkbox"
                                       id="topping-{{ Str::slug($topping) }}"
                                       class="hidden topping-checkbox">
                                <div class="w-5 h-5 border-2 border-gray-300 rounded mr-3 flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $topping }}</div>
                                    <div class="text-sm text-gray-600">+ {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($price, 2) }}</div>
                                </div>
                            </div>
                            <div class="text-primary-red font-semibold">+ {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($price, 2) }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Quantity & Add to Cart -->
                <div class="bg-white pt-8 pb-6 border-t mt-8">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <!-- Quantity -->
                        <div class="flex items-center">
                            <span class="mr-4 font-medium text-gray-900">Quantity:</span>
                            <div class="flex items-center border border-gray-300 rounded-xl">
                                <button class="quantity-btn w-12 h-12 flex items-center justify-center text-gray-600 hover:text-primary-red"
                                        onclick="updateQuantity(-1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number"
                                       id="quantity"
                                       value="1"
                                       min="1"
                                       max="10"
                                       class="w-16 h-12 text-center border-x border-gray-300 text-lg font-semibold">
                                <button class="quantity-btn w-12 h-12 flex items-center justify-center text-gray-600 hover:text-primary-red"
                                        onclick="updateQuantity(1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Add to Cart / Order Now Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button onclick="addToCartWithOptions()"
                                    class="bg-gradient-to-r from-primary-red to-red-600 text-white px-10 py-4 rounded-xl font-bold text-lg hover:from-red-700 hover:to-red-800 transition duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                <i class="fas fa-cart-plus mr-3"></i>
                                Add to Cart
                                <span id="totalPrice" class="ml-2">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($menu->offer_price ?? $menu->price, 2) }}</span>
                            </button>
                            <button onclick="orderNowWithOptions()"
                                    class="bg-gradient-to-r from-primary-yellow to-amber-400 text-gray-900 px-8 py-4 rounded-xl font-bold text-lg hover:from-amber-400 hover:to-primary-yellow transition duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                <i class="fas fa-bolt mr-3"></i>
                                Order Now
                            </button>
                        </div>
                    </div>

                    <!-- Special Instructions -->
                    <div class="mt-6">
                        <textarea id="specialInstructions"
                                  placeholder="Any special instructions? (e.g., no onions, extra spicy)"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-red focus:border-transparent"
                                  rows="2"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Items -->
        @if($relatedItems->count() > 0)
        <div class="mt-20">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">You Might Also Like</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @foreach($relatedItems as $relatedItem)
                <a href="{{ route('menu.show', $relatedItem->id) }}"
                   class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                    <div class="h-40 overflow-hidden">
                        <img src="{{ $relatedItem->image_url }}"
                             alt="{{ $relatedItem->name }}"
                             class="w-full h-full object-cover">
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 mb-2">{{ $relatedItem->name }}</h4>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-primary-red">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($relatedItem->price, 2) }}</span>
                            <button onclick="event.preventDefault(); addToCart({{ $relatedItem->id }})"
                                    class="bg-primary-red text-white px-3 py-1 rounded-lg hover:bg-red-700">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<form id="directCheckoutForm" action="{{ route('checkout.direct', [], false) }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="item_id" value="{{ $menu->id }}">
    <input type="hidden" name="quantity" id="directQuantity" value="1">
    <input type="hidden" name="size" id="directSize">
    <input type="hidden" name="flavor" id="directFlavor">
    <input type="hidden" name="special_instructions" id="directInstructions">
    <div id="directToppingsFields"></div>
</form>
@endsection

@section('scripts')
<script>
    // Initialize variables
    const currencySymbol = @json(config('restaurant.payment.currency_symbol', 'LKR '));
    let selectedSize = null;
    let selectedSizePrice = {{ $menu->offer_price ?? $menu->price }};
    let selectedFlavor = null;
    let selectedFlavorPrice = 0;
    let selectedToppings = [];
    let basePrice = {{ $menu->offer_price ?? $menu->price }};
    let selectedGradient = null;
    const baseImageSrc = document.getElementById('mainImage')?.getAttribute('src');
    const imageFrame = document.getElementById('imageFrame');
    const imageOverlay = document.getElementById('imageOverlay');
    const variantImages = @json($variantImages);

    function normalizeKey(value) {
        return String(value || '').trim().toLowerCase();
    }

    function findVariantImage(flavor, size) {
        if (!Array.isArray(variantImages)) return null;
        if (!flavor || !size) return null;
        const flavorKey = normalizeKey(flavor);
        const sizeKey = normalizeKey(size);
        const match = variantImages.find(item => {
            if (!item) return false;
            return normalizeKey(item.flavor) === flavorKey && normalizeKey(item.size) === sizeKey;
        });
        return match ? match.image : null;
    }

    // Animate flavor items on scroll
    document.addEventListener('DOMContentLoaded', function() {
        // Animate flavors
        const flavors = document.querySelectorAll('.flavor-item');
        flavors.forEach((flavor, index) => {
            setTimeout(() => {
                flavor.classList.add('visible');
            }, index * 200);
        });

        // Select first size by default
        @if($menu->sizes)
            const sizes = @json($sizesRaw);
            const firstSize = Object.keys(sizes)[0];
            if(firstSize) {
                const firstSizeSlug = firstSize.toLowerCase()
                    .replace(/ /g, '-')
                    .replace(/[&'"(),#]/g, '')
                    .replace(/-+/g, '-')
                    .replace(/^-+|-+$/g, '');
                const firstOption = document.getElementById(`size-${firstSizeSlug}`);
                if(firstOption) {
                    selectSize(firstSize, sizes[firstSize], '{{ $firstSizeKey ? ($sizeGradients[$firstSizeKey] ?? "") : "" }}');
                }
            }
        @endif

        // Select first flavor by default
        @if(count($flavorOptions) > 0)
            const firstFlavor = @json($flavorOptions[0]);
            if (firstFlavor && firstFlavor.name) {
                selectFlavor(firstFlavor.name, parseFloat(firstFlavor.price || 0), firstFlavor.image || '', firstFlavor.color || '#DC2626');
            }
        @endif
    });

    function selectSize(size, price, gradient) {
        // Generate slug for ID lookup
        const sizeSlug = size.toLowerCase()
            .replace(/ /g, '-')
            .replace(/[&'"(),#]/g, '')
            .replace(/-+/g, '-')
            .replace(/^-+|-+$/g, '');

        // Remove active class from all sizes
        document.querySelectorAll('.size-option').forEach(option => {
            option.classList.remove('active');
            option.style.borderColor = '#E5E7EB';
        });

        // Add active class to selected size
        const selectedOption = document.getElementById(`size-${sizeSlug}`);
        if(selectedOption) {
            selectedOption.classList.add('active');
            selectedOption.style.borderColor = '#DC2626';
        }

        selectedSize = size;
        selectedSizePrice = price;
        selectedGradient = gradient || null;
        applyMedia();
        calculateTotal();
    }

    function selectFlavor(flavor, price, image, color) {
        document.querySelectorAll('.flavor-card').forEach(card => {
            card.classList.remove('active');
        });

        const cards = Array.from(document.querySelectorAll('.flavor-card'));
        const activeCard = cards.find(card => card.textContent.includes(flavor));
        if (activeCard) {
            activeCard.classList.add('active');
        }

        selectedFlavor = flavor;
        selectedFlavorPrice = parseFloat(price || 0);
        applyMedia(image, color);
        calculateTotal();
    }

    function applyMedia(image, color) {
        if (!imageFrame || !imageOverlay) return;
        imageFrame.classList.add('is-animating');
        const hasImageParam = arguments.length > 0;

        const overlay = selectedGradient
            ? selectedGradient
            : (color ? `linear-gradient(135deg, ${color}55, rgba(251,191,36,0.25))` : 'linear-gradient(135deg, rgba(220,38,38,0.25), rgba(251,191,36,0.25))');
        imageOverlay.style.background = overlay;

        const variantImage = findVariantImage(selectedFlavor, selectedSize);
        let resolvedImage = variantImage || image;
        if (!resolvedImage || (typeof resolvedImage === 'string' && resolvedImage.includes('unsplash.com'))) {
            if (color) {
                resolvedImage = generateFlavorImage(color, selectedFlavor || 'Category');
            }
        }

        if (resolvedImage && document.getElementById('mainImage')) {
            const mainImage = document.getElementById('mainImage');
            mainImage.style.opacity = '0.6';
            setTimeout(() => {
                mainImage.src = resolvedImage;
                mainImage.style.opacity = '1';
            }, 180);
        } else if (hasImageParam && document.getElementById('mainImage') && baseImageSrc) {
            document.getElementById('mainImage').src = baseImageSrc;
        }

        setTimeout(() => {
            imageFrame.classList.remove('is-animating');
        }, 500);
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

    function toggleTopping(topping, price) {
        // Use the same slug logic as Str::slug() in PHP
        const toppingId = 'topping-' + topping.toLowerCase()
            .replace(/ /g, '-')
            .replace(/[&'"(),#]/g, '')
            .replace(/-+/g, '-')
            .replace(/^-+|-+$/g, '');
        const checkbox = document.getElementById(toppingId);
        const item = checkbox.parentElement.parentElement;

        if(checkbox.checked) {
            // Remove topping
            checkbox.checked = false;
            item.style.backgroundColor = '#F9FAFB';
            item.querySelector('.fa-check').style.color = 'transparent';
            selectedToppings = selectedToppings.filter(t => t.name !== topping);
        } else {
            // Add topping
            checkbox.checked = true;
            item.style.backgroundColor = '#FEF3C7';
            item.querySelector('.fa-check').style.color = '#DC2626';
            selectedToppings.push({ name: topping, price: price });
        }

        calculateTotal();
    }

    function updateQuantity(change) {
        const quantityInput = document.getElementById('quantity');
        let quantity = parseInt(quantityInput.value) + change;

        if(quantity < 1) quantity = 1;
        if(quantity > 10) quantity = 10;

        quantityInput.value = quantity;
        calculateTotal();
    }

    function calculateTotal() {
        const quantity = parseInt(document.getElementById('quantity').value);
        let total = (selectedSizePrice + selectedFlavorPrice) * quantity;

        // Add toppings price
        selectedToppings.forEach(topping => {
            total += topping.price * quantity;
        });

        // Update display
        document.getElementById('totalPrice').textContent = currencySymbol + total.toFixed(2);
    }

    function addToCartWithOptions() {
        const quantity = parseInt(document.getElementById('quantity').value);
        const specialInstructions = document.getElementById('specialInstructions').value;

        fetch('{{ route("cart.add", [], false) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                item_id: {{ $menu->id }},
                quantity: quantity,
                size: selectedSize,
                flavor: selectedFlavor,
                toppings: selectedToppings.map(t => t.name),
                special_instructions: specialInstructions
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                showToast('Added to cart!', 'success');
                updateCartCount(data.cart_count);

                // Reset form
                document.getElementById('quantity').value = 1;
                document.getElementById('specialInstructions').value = '';
                selectedToppings.forEach(topping => {
                    const toppingId = 'topping-' + topping.name.toLowerCase()
                        .replace(/ /g, '-')
                        .replace(/[&'"(),#]/g, '')
                        .replace(/-+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    const checkbox = document.getElementById(toppingId);
                    if(checkbox) {
                        checkbox.checked = false;
                        checkbox.parentElement.parentElement.style.backgroundColor = '#F9FAFB';
                        checkbox.parentElement.querySelector('.fa-check').style.color = 'transparent';
                    }
                });
                selectedToppings = [];
                selectedFlavor = null;
                selectedFlavorPrice = 0;
                calculateTotal();
            } else if (data.message) {
                showToast(data.message, 'error');
            }
        });
    }

    function orderNowWithOptions() {
        const quantity = parseInt(document.getElementById('quantity').value);
        const specialInstructions = document.getElementById('specialInstructions').value;

        document.getElementById('directQuantity').value = quantity;
        document.getElementById('directSize').value = selectedSize || '';
        document.getElementById('directFlavor').value = selectedFlavor || '';
        document.getElementById('directInstructions').value = specialInstructions || '';

        const toppingsContainer = document.getElementById('directToppingsFields');
        toppingsContainer.innerHTML = '';
        selectedToppings.forEach((topping) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'toppings[]';
            input.value = topping.name;
            toppingsContainer.appendChild(input);
        });

        document.getElementById('directCheckoutForm').submit();
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

    // Listen for quantity input changes
    document.getElementById('quantity')?.addEventListener('change', function() {
        let value = parseInt(this.value);
        if(value < 1) this.value = 1;
        if(value > 10) this.value = 10;
        calculateTotal();
    });
</script>
@endsection


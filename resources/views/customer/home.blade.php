@extends('layouts.app')

@section('title', 'Home - PaSSSna Restaurant')

@section('content')
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-primary-red/5 to-primary-yellow/5">
        <div class="absolute inset-0 bg-grid-slate-100 [mask-image:linear-gradient(0deg,white,rgba(255,255,255,0.6))]"></div>
        <div class="relative container mx-auto px-4 py-20 md:py-32">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="text-left animate-fade-in">
                    <img src="{{ asset('PASSSNA.png') }}" alt="PaSSSna Logo" class="h-16 w-auto mb-6">
                    <h1 class="text-5xl md:text-7xl font-bold text-gray-900 mb-6 leading-tight">
                        Taste the
                        <span class="relative">
                            <span class="relative z-10">Passion</span>
                            <span class="absolute bottom-2 left-0 w-full h-4 bg-primary-yellow/30 -z-10"></span>
                        </span>
                        at PaSSSna
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 max-w-xl">
                        Where every dish tells a story. Experience culinary excellence with our authentic flavors, premium ingredients, and exceptional service.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('menu') }}"
                           class="group relative inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white bg-gradient-to-r from-primary-red to-red-600 rounded-xl hover:from-red-700 hover:to-red-800 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <i class="fas fa-utensils mr-3 group-hover:rotate-12 transition-transform duration-300"></i>
                            Explore Our Menu
                            <span class="absolute inset-0 border-2 border-transparent group-hover:border-white/30 rounded-xl transition-all duration-300"></span>
                        </a>
                        <a href="{{ route('reservation.create') }}"
                           class="group inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-gray-800 bg-primary-yellow rounded-xl hover:bg-yellow-500 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <i class="fas fa-calendar-check mr-3 group-hover:scale-110 transition-transform duration-300"></i>
                            Book a Table
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="flex flex-wrap gap-8 mt-12 pt-8 border-t border-gray-200">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-red">500+</div>
                            <div class="text-gray-600">Happy Customers</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-primary-yellow">50+</div>
                            <div class="text-gray-600">Menu Items</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-navy-blue">24/7</div>
                            <div class="text-gray-600">Online Orders</div>
                        </div>
                    </div>
                </div>

                <!-- Right Image -->
                <div class="relative animate-float">
                    <div class="relative z-10">
                        <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                             alt="Restaurant Interior"
                             class="rounded-2xl shadow-2xl">
                    </div>
                    <!-- Floating Elements -->
                    <div class="absolute -top-6 -left-6 w-24 h-24 bg-primary-yellow rounded-2xl -z-10"></div>
                    <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-primary-red rounded-3xl -z-10"></div>
                    <div class="absolute top-1/2 -right-8 w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center">
                        <i class="fas fa-star text-primary-yellow text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce-slow">
            <div class="flex flex-col items-center">
                <span class="text-gray-600 text-sm mb-2">Scroll to explore</span>
                <i class="fas fa-chevron-down text-primary-red text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Fast Moving Meals Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-primary-red/10 text-primary-red rounded-full text-sm font-semibold mb-4">
                    🔥 Hot & Trending
                </span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Fast Moving Meals</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Our customers can't get enough of these popular dishes</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($fastMovingMeals as $index => $meal)
                <div class="group menu-item-hover bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 cursor-pointer"
                     onclick="window.location.href='{{ route('menu.show', $meal->id) }}'">
                    <!-- Image -->
                    <div class="relative overflow-hidden">
                        <img src="{{ $meal->image_url }}"
                             alt="{{ $meal->name }}"
                             class="w-full h-56 object-cover menu-img">

                        <!-- Offer Badge -->
                        @if($meal->offer_price && $meal->price > 0)
                        <div class="absolute top-4 right-4">
                            <span class="bg-primary-red text-white px-3 py-1 rounded-full text-sm font-bold shadow-lg">
                                Save {{ round((($meal->price - $meal->offer_price) / $meal->price) * 100) }}%
                            </span>
                        </div>
                        @endif

                        <!-- Quick Add -->
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <button onclick="event.stopPropagation(); quickAddToCart({{ $meal->id }})"
                                    class="bg-white text-primary-red px-6 py-3 rounded-lg font-semibold transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                <i class="fas fa-plus mr-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-xl font-bold text-gray-900">{{ $meal->name }}</h3>
                            <div class="text-right">
                                @if($meal->offer_price)
                                    <span class="text-2xl font-bold text-primary-red">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($meal->offer_price, 2) }}</span>
                                    <span class="text-gray-400 line-through block text-sm">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($meal->price, 2) }}</span>
                                @else
                                    <span class="text-2xl font-bold text-gray-900">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($meal->price, 2) }}</span>
                                @endif
                            </div>
                        </div>

                        <p class="text-gray-600 mb-4">{{ Str::limit($meal->description, 100) }}</p>

                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= 4 ? 'text-yellow-400' : 'text-gray-300' }} text-sm"></i>
                                @endfor
                                <span class="text-gray-500 ml-2 text-sm">(4.5)</span>
                            </div>

                            <div class="flex items-center space-x-2">
                                <span class="text-gray-500 text-sm">
                                    <i class="fas fa-clock mr-1"></i>{{ $meal->preparation_time }} min
                                </span>
                                <button onclick="event.stopPropagation(); addToCart({{ $meal->id }})"
                                        class="bg-primary-red text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300">
                                    <i class="fas fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- View All Button -->
            <div class="text-center mt-12">
                <a href="{{ route('menu') }}" class="inline-flex items-center text-primary-red hover:text-red-700 font-semibold text-lg">
                    View Full Menu
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform duration-300"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Special Offers -->
    <section class="py-20 bg-gradient-to-r from-primary-red/5 via-primary-yellow/5 to-primary-red/5">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-primary-yellow text-gray-800 rounded-full text-sm font-semibold mb-4">
                    🎉 Special Offers
                </span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Limited Time Deals</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Don't miss out on our exclusive offers</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($offers as $offer)
                <div class="bg-gradient-to-r from-primary-red to-primary-yellow p-1 rounded-2xl transform hover:scale-105 transition-transform duration-300">
                    <div class="bg-white rounded-2xl p-8 h-full">
                        <div class="flex items-start mb-6">
                            <div class="w-20 h-20 bg-gradient-to-r from-primary-red to-primary-yellow rounded-2xl flex items-center justify-center mr-6">
                                <i class="fas fa-gift text-white text-3xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $offer->name }}</h3>
                                <div class="flex items-center text-gray-600">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <span>Valid until {{ $offer->offer_valid_until->format('F d, Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <p class="text-gray-700 mb-6">{{ $offer->description }}</p>

                        <div class="flex justify-between items-center">
                            <div>
                                <span class="text-3xl font-bold text-primary-red">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($offer->offer_price, 2) }}</span>
                                <span class="text-gray-400 line-through text-xl ml-3">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($offer->price, 2) }}</span>
                                <div class="text-sm text-gray-500 mt-1">
                                    Save {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($offer->price - $offer->offer_price, 2) }}
                                </div>
                            </div>
                            <a href="{{ route('menu.show', $offer->id) }}"
                               class="bg-gradient-to-r from-primary-red to-primary-yellow text-white px-8 py-3 rounded-xl font-semibold hover:shadow-xl transition-all duration-300">
                                Grab This Deal
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">How It Works</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Simple steps to enjoy our delicious food</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-8 rounded-2xl bg-gradient-to-b from-primary-red/5 to-transparent hover:shadow-xl transition-all duration-300">
                    <div class="w-20 h-20 bg-primary-red rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-white text-2xl font-bold">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Browse & Select</h3>
                    <p class="text-gray-600">Explore our menu and choose your favorite dishes</p>
                </div>

                <div class="text-center p-8 rounded-2xl bg-gradient-to-b from-primary-yellow/5 to-transparent hover:shadow-xl transition-all duration-300">
                    <div class="w-20 h-20 bg-primary-yellow rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-gray-800 text-2xl font-bold">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Customize & Order</h3>
                    <p class="text-gray-600">Customize your meal and place your order online</p>
                </div>

                <div class="text-center p-8 rounded-2xl bg-gradient-to-b from-navy-blue/5 to-transparent hover:shadow-xl transition-all duration-300">
                    <div class="w-20 h-20 bg-navy-blue rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-white text-2xl font-bold">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Enjoy & Relax</h3>
                    <p class="text-gray-600">Receive your order and enjoy delicious food</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Customer Reviews -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-primary-yellow/20 text-primary-yellow rounded-full text-sm font-semibold mb-4">
                    ❤️ Loved by Customers
                </span>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">What Our Customers Say</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Real experiences from our valued guests</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($reviews as $index => $review)
                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 animate-slide-up"
                     style="animation-delay: {{ $index * 0.1 }}s">
                    <div class="flex items-center mb-6">
                        <div class="w-14 h-14 rounded-full overflow-hidden mr-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($review->customer_name) }}&background=random&color=fff&size=128"
                                 alt="{{ $review->customer_name }}"
                                 class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">{{ $review->customer_name }}</h4>
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }} text-sm"></i>
                                @endfor
                                <span class="text-gray-500 text-sm ml-2">{{ $review->rating }}.0</span>
                            </div>
                        </div>
                    </div>

                    <p class="text-gray-700 italic mb-6">"{{ $review->comment }}"</p>

                    <div class="flex justify-between items-center text-gray-500 text-sm">
                        <span>{{ $review->created_at->format('M d, Y') }}</span>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>Verified Order</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Add Review Button -->
            <div class="text-center mt-12">
                <a href="{{ route('contact') }}" class="inline-flex items-center px-6 py-3 bg-white border-2 border-primary-red text-primary-red rounded-xl hover:bg-primary-red hover:text-white transition-all duration-300 font-semibold">
                    <i class="fas fa-pen mr-2"></i>
                    Write a Review
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-navy-blue text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">Ready to Experience PaSSSna?</h2>
                <p class="text-xl mb-10 text-white/90">Join us for an unforgettable dining experience. Reserve your table now or explore our menu.</p>

                <div class="flex flex-wrap justify-center gap-6">
                    <a href="{{ route('reservation.create') }}"
                       class="group inline-flex items-center justify-center px-10 py-4 text-lg font-semibold text-navy-blue bg-primary-yellow rounded-xl hover:bg-yellow-500 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <i class="fas fa-calendar-alt mr-3 group-hover:scale-110 transition-transform duration-300"></i>
                        Book a Table
                    </a>

                    <a href="{{ route('menu') }}"
                       class="group inline-flex items-center justify-center px-10 py-4 text-lg font-semibold text-white border-2 border-white rounded-xl hover:bg-white hover:text-navy-blue transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-utensils mr-3 group-hover:rotate-12 transition-transform duration-300"></i>
                        View Full Menu
                    </a>

                    <a href="tel:+15551234567"
                       class="group inline-flex items-center justify-center px-10 py-4 text-lg font-semibold text-white bg-primary-red rounded-xl hover:bg-red-700 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <i class="fas fa-phone-alt mr-3 group-hover:animate-pulse"></i>
                        Call Now
                    </a>
                </div>

                <!-- Contact Info -->
                <div class="mt-12 pt-8 border-t border-white/20">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="flex items-center justify-center">
                            <i class="fas fa-clock text-primary-yellow text-2xl mr-3"></i>
                            <div class="text-left">
                                <div class="font-semibold">Open Hours</div>
                                <div class="text-white/80 text-sm">11 AM - 11 PM Daily</div>
                            </div>
                        </div>
                        <div class="flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-primary-yellow text-2xl mr-3"></i>
                            <div class="text-left">
                                <div class="font-semibold">Location</div>
                                <div class="text-white/80 text-sm">123 Gourmet Street, Food City</div>
                            </div>
                        </div>
                        <div class="flex items-center justify-center">
                            <i class="fas fa-envelope text-primary-yellow text-2xl mr-3"></i>
                            <div class="text-left">
                                <div class="font-semibold">Email</div>
                                <div class="text-white/80 text-sm">info@passsna.com</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    function quickAddToCart(itemId) {
        addToCart(itemId);
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
                showToast('Item added to cart!', 'success');

                // Update cart count
                updateCartCount(data.cart_count);
            }
        });
    }

    function updateCartCount(count) {
        const cartBadge = document.querySelector('.fa-shopping-cart').parentElement.querySelector('span');
        if(cartBadge) {
            cartBadge.textContent = count;
            cartBadge.classList.add('animate-pulse');
            setTimeout(() => cartBadge.classList.remove('animate-pulse'), 1000);
        } else {
            // Create cart badge
            const cartLink = document.querySelector('.fa-shopping-cart').parentElement;
            const badge = document.createElement('span');
            badge.className = 'absolute -top-1 -right-1 bg-primary-red text-white text-xs rounded-full w-5 h-5 flex items-center justify-center animate-pulse';
            badge.textContent = count;
            cartLink.appendChild(badge);
        }
    }
</script>
@endsection


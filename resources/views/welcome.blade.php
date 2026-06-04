@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden text-white">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_#C0392B_0,_#9B1C1C_35%,_#6B0F12_70%,_#4B0B0D_100%)]"></div>
    <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 15% 20%, rgba(255,255,255,0.2) 0, transparent 45%), radial-gradient(circle at 85% 30%, rgba(255,255,255,0.18) 0, transparent 40%);"></div>
    <div class="absolute -top-32 right-10 w-96 h-96 bg-yellow-400/20 rounded-full blur-[140px]"></div>
    <div class="absolute -bottom-24 left-12 w-80 h-80 bg-orange-400/25 rounded-full blur-[120px]"></div>

    <div class="container mx-auto px-4 pt-24 pb-40 md:pt-28 md:pb-48 relative z-10">
        <div class="mb-10 flex justify-center">
            <img src="{{ asset('PASSSNA.png') }}" alt="PaSSSna Logo" class="h-24 md:h-28 object-contain drop-shadow-2xl animate-bounce-slow">
        </div>
        <div class="grid grid-cols-1 gap-12 items-center">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                    Sri Lankan Soul.<br>
                    <span class="text-yellow-300">International Finish.</span>
                </h1>
                <p class="text-lg md:text-xl text-white/90 mb-10 max-w-2xl mx-auto">
                    Crafted with island spice, plated with modern elegance. Discover dishes that feel like home and taste like a celebration.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('menu') }}" class="bg-white text-red-700 px-8 py-4 rounded-full font-semibold text-lg hover:bg-yellow-300 transition-all duration-300 shadow-xl">
                        <i class="fas fa-book-open mr-2"></i>View Our Menu
                    </a>
                    <a href="{{ route('reservation.create') }}" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-semibold text-lg hover:bg-white hover:text-red-700 transition-all duration-300">
                        <i class="fas fa-calendar-check mr-2"></i>Book a Table
                    </a>
                </div>
                <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-2xl mx-auto">
                    <div class="bg-white/10 border border-white/20 rounded-2xl p-4 text-center">
                        <div class="text-2xl font-bold">150+</div>
                        <div class="text-xs uppercase tracking-wide text-white/70">Menu Items</div>
                    </div>
                    <div class="bg-white/10 border border-white/20 rounded-2xl p-4 text-center">
                        <div class="text-2xl font-bold">20</div>
                        <div class="text-xs uppercase tracking-wide text-white/70">Chef Specials</div>
                    </div>
                    <div class="bg-white/10 border border-white/20 rounded-2xl p-4 text-center">
                        <div class="text-2xl font-bold">4.9</div>
                        <div class="text-xs uppercase tracking-wide text-white/70">Avg Rating</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Curve Divider -->
<div class="relative -mt-28">
    <svg viewBox="0 0 1440 180" xmlns="http://www.w3.org/2000/svg" class="block w-full h-auto">
        <defs>
            <linearGradient id="heroFade" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#F3EEE7"/>
                <stop offset="100%" stop-color="#F8F3ED"/>
            </linearGradient>
            <linearGradient id="heroAccent" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#9B1C1C"/>
                <stop offset="70%" stop-color="#B45309"/>
                <stop offset="100%" stop-color="#FBBF24"/>
            </linearGradient>
        </defs>
        <path d="M0 120C220 70 460 60 700 78C940 96 1180 126 1440 98V180H0V120Z" fill="url(#heroFade)"/>
        <path d="M0 100C260 40 500 58 760 88C1020 118 1240 120 1440 86V140H0V100Z" fill="url(#heroAccent)" opacity="0.5"/>
    </svg>
</div>

<!-- Features Section -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 group">
                <div class="w-16 h-16 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-motorcycle text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Fast Delivery</h3>
                <p class="text-gray-600">Hot and fresh meals delivered to your doorstep within 45 minutes.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 group">
                <div class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-star text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Premium Quality</h3>
                <p class="text-gray-600">Fresh ingredients and authentic recipes prepared by expert chefs.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2 group">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-700 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i class="fas fa-heart text-white text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Customer First</h3>
                <p class="text-gray-600">Your satisfaction is our priority with exceptional service.</p>
            </div>
        </div>
    </div>
</section>

<!-- Fast Moving Items Slider -->
@if(isset($fastMovingItems) && $fastMovingItems->count() > 0)
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                <i class="fas fa-fire text-red-500 mr-2"></i>Fast Moving Items
            </h2>
            <p class="text-gray-600">Our most popular dishes loved by customers</p>
        </div>

        <div class="relative">
            <div class="flex overflow-x-auto gap-6 pb-8 scrollbar-hide snap-x" x-data="{ scroll: 0 }">
                @foreach($fastMovingItems as $item)
                <div class="flex-shrink-0 w-72 snap-center">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 group cursor-pointer"
                         onclick="window.location.href='{{ route('menu.show', $item->id) }}'">
                        <div class="relative h-48 bg-gradient-to-br from-red-100 to-yellow-100 overflow-hidden">
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @if($item->offer_price)
                                <span class="absolute top-3 right-3 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-semibold animate-pulse">
                                    {{ $item->discountPercentage }}% OFF
                                </span>
                            @endif
                        </div>
                        <div class="p-6">
                            <h3 class="font-bold text-lg text-gray-800 mb-2">{{ $item->name }}</h3>
                            <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ $item->description }}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    @if($item->offer_price)
                                        <span class="text-gray-400 line-through text-sm">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->price, 2) }}</span>
                                        <span class="text-red-600 font-bold text-xl">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->offer_price, 2) }}</span>
                                    @else
                                        <span class="text-gray-800 font-bold text-xl">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->price, 2) }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('menu.show', $item->id) }}"
                                   onclick="event.stopPropagation();"
                                   class="bg-gradient-to-r from-red-500 to-yellow-500 text-white px-4 py-2 rounded-full hover:scale-105 transition-transform">
                                    <i class="fas fa-plus mr-1"></i>Add
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Navigation Arrows -->
            <button class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 bg-white shadow-lg rounded-full w-12 h-12 items-center justify-center hover:bg-gray-100 transition-colors hidden md:flex" onclick="document.querySelector('.overflow-x-auto').scrollBy({left: -300, behavior: 'smooth'})">
                <i class="fas fa-chevron-left text-gray-600"></i>
            </button>
            <button class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 bg-white shadow-lg rounded-full w-12 h-12 items-center justify-center hover:bg-gray-100 transition-colors hidden md:flex" onclick="document.querySelector('.overflow-x-auto').scrollBy({left: 300, behavior: 'smooth'})">
                <i class="fas fa-chevron-right text-gray-600"></i>
            </button>
        </div>
    </div>
</section>
@endif

<!-- Promotions Section -->
@if(isset($promotions) && $promotions->count() > 0)
<section class="py-16 bg-gradient-to-r from-navy-blue to-blue-700 text-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">
                <i class="fas fa-gift text-yellow-400 mr-2"></i>Special Offers
            </h2>
            <p class="text-white/80">Don't miss out on our exclusive deals</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($promotions as $promo)
            <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20 hover:bg-white/20 transition-all">
                <div class="flex items-center justify-between mb-4">
                    <span class="bg-yellow-400 text-gray-900 px-3 py-1 rounded-full text-sm font-bold">{{ $promo->discount_value }}{{ $promo->discount_type === 'percentage' ? '%' : ' LKR' }} OFF</span>
                    <span class="text-sm text-white/70"><i class="far fa-clock mr-1"></i>Valid until {{ \Carbon\Carbon::parse($promo->end_date)->format('M d, Y') }}</span>
                </div>
                <h3 class="text-xl font-bold mb-2">{{ $promo->title }}</h3>
                <p class="text-white/80 mb-4">{{ $promo->description }}</p>
                <div class="bg-white/10 rounded-lg px-4 py-2 flex items-center justify-between">
                    <span class="font-mono text-lg">{{ $promo->code }}</span>
                    <button onclick="copyToClipboard('{{ $promo->code }}')" class="text-yellow-400 hover:text-yellow-300">
                        <i class="far fa-copy"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Reviews Section -->
@if(isset($reviews) && $reviews->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                <i class="fas fa-comments text-yellow-500 mr-2"></i>Customer Reviews
            </h2>
            <p class="text-gray-600">What our customers say about us</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($reviews as $review)
            <div class="bg-white rounded-2xl p-8 shadow-lg">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-yellow-500 rounded-full flex items-center justify-center text-white font-bold">
                        {{ $review->user ? substr($review->user->name, 0, 1) : 'G' }}
                    </div>
                    <div class="ml-4">
                        <h4 class="font-semibold text-gray-800">{{ $review->user->name ?? 'Guest' }}</h4>
                        <div class="flex text-yellow-400 text-sm">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $review->rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                    </div>
                </div>
                <p class="text-gray-600 italic">"{{ $review->comment }}"</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-red-600 to-yellow-500 text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Experience PaSSSna?</h2>
        <p class="text-xl mb-8 text-white/90">Order now or book a table for an unforgettable dining experience</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('menu') }}" class="bg-white text-red-600 px-8 py-4 rounded-full font-semibold text-lg hover:bg-gray-100 transition-all hover:scale-105 shadow-lg">
                <i class="fas fa-shopping-bag mr-2"></i>Order Now
            </a>
            <a href="{{ route('reservation.create') }}" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-semibold text-lg hover:bg-white hover:text-red-600 transition-all">
                <i class="fas fa-calendar-alt mr-2"></i>Make Reservation
            </a>
        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Promo code copied to clipboard',
                timer: 2000,
                showConfirmButton: false
            });
        });
    }
</script>
@endsection


@extends('layouts.app')

@section('title', 'Checkout - PaSSSna Restaurant')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    /* Custom animations */
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 5px rgba(251, 191, 36, 0.5); }
        50% { box-shadow: 0 0 20px rgba(251, 191, 36, 0.8); }
    }

    @keyframes fadeInScale {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }

    .animate-slide-in { animation: slideIn 0.4s ease-out forwards; }
    .animate-pulse-glow { animation: pulse-glow 2s infinite; }
    .animate-fade-in { animation: fadeInScale 0.3s ease-out forwards; }

    /* Delivery option hover effects */
    .delivery-option {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .delivery-option:hover {
        transform: translateY(-2px);
    }

    .delivery-option.active {
        border-color: #FBBF24 !important;
        background-color: #FEF3C7 !important;
        box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
    }

    /* Payment method hover effects */
    .payment-method {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .payment-method:hover {
        transform: translateY(-2px);
    }

    .payment-method.active {
        border-color: #FBBF24 !important;
        background-color: #FEF3C7 !important;
        box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
    }

    /* Card input focus effects */
    .input-focus:focus {
        border-color: #FBBF24;
        box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.2);
        outline: none;
    }

    /* Order summary card */
    .order-summary-card {
        background: linear-gradient(135deg, #FFF 0%, #FEF9E7 100%);
    }

    /* Promo code button */
    .promo-btn {
        background: linear-gradient(135deg, #DC2626 0%, #F59E0B 100%);
        transition: all 0.3s ease;
    }

    .promo-btn:hover {
        background: linear-gradient(135deg, #B91C1C 0%, #D97706 100%);
        transform: translateY(-1px);
    }

    /* Place order button */
    .place-order-btn {
        background: linear-gradient(135deg, #DC2626 0%, #EA580C 100%);
        transition: all 0.3s ease;
    }

    .place-order-btn:hover {
        background: linear-gradient(135deg, #B91C1C 0%, #C2410C 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
    }

    /* Cart item animation */
    .cart-item {
        transition: all 0.3s ease;
    }

    .cart-item:hover {
        background-color: #FEF3C7;
        padding-left: 1rem;
        margin-left: -1rem;
    }

    /* Section card hover */
    .section-card {
        transition: all 0.3s ease;
    }

    .section-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    /* Free delivery badge */
    .free-delivery-badge {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        animation: pulse-glow 2s infinite;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 text-center animate-slide-in">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                <i class="fas fa-check-circle text-amber-500 mr-3"></i>Checkout
            </h1>
            <p class="text-gray-600 text-lg">Complete your order</p>
        </div>

        @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-r-lg animate-slide-in">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                {{ session('error') }}
            </div>
        </div>
        @endif

        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-r-lg animate-slide-in">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                {{ session('success') }}
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-r-lg animate-slide-in">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                <span class="font-medium">Please fix the following errors:</span>
            </div>
            <ul class="list-disc list-inside ml-6">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(session('promoError'))
        <div class="mb-6 bg-orange-50 border-l-4 border-orange-500 text-orange-700 px-4 py-3 rounded-r-lg animate-slide-in">
            <div class="flex items-center">
                <i class="fas fa-tag text-orange-500 mr-3"></i>
                {{ session('promoError') }}
            </div>
        </div>
        @endif

        <form id="promoForm" action="{{ route('checkout.apply-promo') }}" method="POST" class="hidden">
            @csrf
        </form>

        <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
            @csrf
            <input type="hidden" name="delivery_type" id="deliveryType" value="takeaway">
            <input type="hidden" name="payment_method" id="paymentMethod" value="cash">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Forms -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Delivery Type -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 section-card animate-fade-in" style="animation-delay: 0.1s;">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">
                            <i class="fas fa-truck mr-2 text-amber-500"></i>Delivery Type
                        </h2>
                        <div class="grid grid-cols-3 gap-4">
                            <div onclick="selectDelivery('takeaway')" class="delivery-option cursor-pointer p-4 rounded-xl border-2 border-gray-200 hover:border-amber-400 bg-gradient-to-br from-gray-50 to-white">
                                <div class="text-center">
                                    <i class="fas fa-shopping-bag text-3xl text-amber-600 mb-3"></i>
                                    <p class="font-semibold text-gray-900">Takeaway</p>
                                    <p class="text-sm text-gray-500">15-20 min</p>
                                </div>
                            </div>
                            <div onclick="selectDelivery('delivery')" class="delivery-option cursor-pointer p-4 rounded-xl border-2 border-gray-200 hover:border-amber-400 bg-gradient-to-br from-gray-50 to-white">
                                <div class="text-center">
                                    <i class="fas fa-motorcycle text-3xl text-amber-600 mb-3"></i>
                                    <p class="font-semibold text-gray-900">Delivery</p>
                                    <p class="text-sm text-gray-500">30-45 min</p>
                                </div>
                            </div>
                            <div onclick="selectDelivery('dine_in')" class="delivery-option cursor-pointer p-4 rounded-xl border-2 border-gray-200 hover:border-amber-400 bg-gradient-to-br from-gray-50 to-white">
                                <div class="text-center">
                                    <i class="fas fa-utensils text-3xl text-amber-600 mb-3"></i>
                                    <p class="font-semibold text-gray-900">Dine In</p>
                                    <p class="text-sm text-gray-500">Immediate</p>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Address (hidden for takeaway/dine-in) -->
                        <div id="deliveryAddressSection" class="mt-6 hidden animate-slide-in">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>Delivery Address
                            </label>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <input type="text" name="address[street]" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 input-focus transition-all" placeholder="Street Address *">
                                </div>
                                <div>
                                    <input type="text" name="address[apartment]" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 input-focus transition-all" placeholder="Apartment, Suite, Unit (optional)">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <input type="text" name="address[city]" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 input-focus transition-all" placeholder="City *">
                                    </div>
                                    <div>
                                        <input type="text" name="address[zip]" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 input-focus transition-all" placeholder="ZIP Code *">
                                    </div>
                                </div>
                            </div>
                            @error('address.street')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('address.city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('address.zip')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 section-card animate-fade-in" style="animation-delay: 0.2s;">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">
                            <i class="fas fa-user mr-2 text-amber-500"></i>Contact Information
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user-circle mr-2 text-amber-500"></i>Name
                                </label>
                                <input type="text" name="contact[name]" value="{{ auth()->user()->name }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 input-focus transition-all" required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-phone mr-2 text-amber-500"></i>Phone
                                </label>
                                <input type="text" name="contact[phone]" value="{{ auth()->user()->phone ?? '' }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 input-focus transition-all" placeholder="(XXX) XXX-XXXX">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-envelope mr-2 text-amber-500"></i>Email
                                </label>
                                <input type="email" name="contact[email]" value="{{ auth()->user()->email }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 input-focus transition-all" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-sticky-note mr-2 text-amber-500"></i>Special Instructions
                                </label>
                                <textarea name="special_instructions" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 input-focus transition-all" placeholder="Any special requests for your order?"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 section-card animate-fade-in" style="animation-delay: 0.3s;">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">
                            <i class="fas fa-credit-card mr-2 text-amber-500"></i>Payment Method
                        </h2>
                        <div class="grid grid-cols-2 gap-4">
                            <div onclick="selectPayment('cash')" class="payment-method cursor-pointer p-4 rounded-xl border-2 border-gray-200 hover:border-amber-400 bg-gradient-to-br from-gray-50 to-white">
                                <div class="text-center">
                                    <i class="fas fa-money-bill-wave text-3xl text-amber-600 mb-3"></i>
                                    <p class="font-semibold text-gray-900">Cash</p>
                                    <p class="text-sm text-gray-500">Pay upon delivery</p>
                                </div>
                            </div>
                            <div onclick="selectPayment('card')" class="payment-method cursor-pointer p-4 rounded-xl border-2 border-gray-200 hover:border-amber-400 bg-gradient-to-br from-gray-50 to-white">
                                <div class="text-center">
                                    <i class="fas fa-credit-card text-3xl text-amber-600 mb-3"></i>
                                    <p class="font-semibold text-gray-900">Card</p>
                                    <p class="text-sm text-gray-500">Pay now</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Details (hidden unless card selected) -->
                        <div id="cardDetails" class="mt-6 hidden animate-slide-in">
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-credit-card mr-2 text-amber-500"></i>Card Number
                                    </label>
                                    <input type="text" name="card[number]" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 input-focus transition-all" placeholder="1234 5678 9012 3456">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-calendar mr-2 text-amber-500"></i>Expiry Date
                                        </label>
                                        <input type="text" name="card[expiry]" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 input-focus transition-all" placeholder="MM/YY">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <i class="fas fa-lock mr-2 text-amber-500"></i>CVV
                                        </label>
                                        <input type="text" name="card[cvv]" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 input-focus transition-all" placeholder="123">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-id-card mr-2 text-amber-500"></i>Cardholder Name
                                    </label>
                                    <input type="text" name="card[name]" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 input-focus transition-all" placeholder="John Doe">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Order -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 section-card animate-fade-in" style="animation-delay: 0.4s;">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">
                                    <i class="fas fa-clock mr-2 text-amber-500"></i>Schedule Order
                                </h2>
                                <p class="text-sm text-gray-500">Order for later (optional)</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="schedule_order" id="scheduleOrder" class="sr-only peer" onchange="toggleSchedule()">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-amber-600"></div>
                            </label>
                        </div>
                        <div id="scheduleOptions" class="mt-4 hidden grid-cols-2 gap-4 animate-slide-in">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-calendar-day mr-2 text-amber-500"></i>Date
                                </label>
                                <input type="date" name="schedule_date" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 input-focus transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-clock mr-2 text-amber-500"></i>Time
                                </label>
                                <input type="time" name="schedule_time" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 input-focus transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 sticky top-4 order-summary-card animate-fade-in" style="animation-delay: 0.5s;">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">
                            <i class="fas fa-receipt mr-2 text-amber-500"></i>Order Summary
                        </h2>

                        <!-- Cart Items -->
                        <div class="space-y-3 mb-4 max-h-72 overflow-y-auto scrollbar-hide">
                            @forelse($cart as $item)
                            <div class="cart-item flex justify-between items-center py-3 border-b border-gray-100 last:border-0 rounded-lg px-3">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ $item['name'] }}</p>
                                    <p class="text-sm text-gray-500">Qty: {{ $item['quantity'] }} x {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item['price'], 2) }}</p>
                                </div>
                                <p class="font-bold text-amber-600">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                            </div>
                            @empty
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-shopping-bag text-4xl text-gray-300 mb-3"></i>
                                <p>Your cart is empty</p>
                            </div>
                            @endforelse
                        </div>

                        <!-- Promotions Applied -->
                        @if(isset($appliedPromotions) && count($appliedPromotions) > 0)
                        <div class="mb-4 p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                            <p class="text-sm font-semibold text-green-800 mb-2">
                                <i class="fas fa-tag mr-2"></i>Applied Promotions:
                            </p>
                            @foreach($appliedPromotions as $promo)
                            <div class="flex justify-between text-sm">
                                <span class="text-green-700">{{ $promo['code'] }}</span>
                                <span class="text-green-700 font-semibold">-{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($promo['discount'], 2) }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <!-- Totals -->
                        <div class="space-y-3 pt-4 border-t border-gray-200">
                            <div class="flex justify-between text-gray-600">
                                <span><i class="fas fa-calculator mr-2 text-gray-400"></i>Subtotal</span>
                                <span class="font-semibold">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($subtotal, 2) }}</span>
                            </div>
                            @if(isset($discount) && $discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span><i class="fas fa-percent mr-2 text-green-400"></i>Discount</span>
                                <span class="font-semibold">-{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($discount, 2) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between text-gray-600">
                                <span><i class="fas fa-file-invoice-dollar mr-2 text-gray-400"></i>Tax ({{ $taxRate ?? 10 }}%)</span>
                                <span class="font-semibold">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($tax, 2) }}</span>
                            </div>
                            @if(isset($serviceCharge) && $serviceCharge > 0)
                            <div class="flex justify-between text-gray-600">
                                <span><i class="fas fa-concierge-bell mr-2 text-gray-400"></i>Service Charge</span>
                                <span class="font-semibold">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($serviceCharge, 2) }}</span>
                            </div>
                            @endif

                            <!-- Delivery Fee -->
                            <div class="flex justify-between text-gray-600" id="deliveryFeeRow">
                                <span><i class="fas fa-truck mr-2 text-gray-400"></i>Delivery Fee</span>
                                <span class="font-semibold" id="deliveryFeeDisplay">
                                    @if($deliveryFee > 0)
                                        {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($deliveryFee, 2) }}
                                    @else
                                        <span class="text-green-600 font-bold">Free</span>
                                    @endif
                                </span>
                            </div>

                            <!-- Free Delivery Progress -->
                            @if($freeDeliveryThreshold > 0 && $subtotal < $freeDeliveryThreshold)
                            <div class="mt-2 p-3 bg-amber-50 rounded-lg border border-amber-200">
                                <p class="text-sm text-amber-800 mb-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Add {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($freeDeliveryThreshold - $subtotal, 2) }} more for free delivery!
                                </p>
                                <div class="w-full bg-amber-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-amber-500 to-orange-500 h-2 rounded-full transition-all duration-500" style="width: {{ min(($subtotal / $freeDeliveryThreshold) * 100, 100) }}%"></div>
                                </div>
                            </div>
                            @elseif($freeDeliveryThreshold > 0 && $subtotal >= $freeDeliveryThreshold)
                            <div class="mt-2 p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200 free-delivery-badge">
                                <p class="text-sm font-semibold text-green-800">
                                    <i class="fas fa-gift mr-2"></i>🎉 Free Delivery Applied!
                                </p>
                            </div>
                            @endif

                            <div class="flex justify-between text-2xl font-bold text-gray-900 pt-3 border-t-2 border-gray-200 mt-3">
                                <span><i class="fas fa-check-double mr-2 text-amber-500"></i>Total</span>
                                <span class="text-amber-600" id="orderTotal">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        <!-- Estimated Time -->
                        <div class="mt-4 p-4 bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl border border-amber-200">
                            <div class="flex items-center text-amber-800">
                                <i class="far fa-clock mr-3 text-xl"></i>
                                <div>
                                    <span class="text-sm font-semibold">Estimated Time:</span>
                                    <span class="font-bold ml-1" id="estimatedTime">15-20 minutes</span>
                                </div>
                            </div>
                        </div>

                        <!-- Promo Code -->
                        @if(!isset($promoError) || !$promoError)
                        <div class="mt-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-tag mr-2 text-red-500"></i>Have a promo code?
                            </label>
                            <div class="flex gap-2">
                                <input type="text" name="promo_code" form="promoForm" class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 input-focus transition-all" placeholder="Enter promo code">
                                <button type="submit" form="promoForm" class="px-6 py-3 promo-btn text-white font-semibold rounded-xl transition-all">
                                    <i class="fas fa-paper-plane mr-2"></i>Apply
                                </button>
                            </div>
                        </div>
                        @endif

                        <!-- Terms -->
                        <div class="mt-4">
                            <label class="flex items-start">
                                <input type="checkbox" id="terms" class="mt-1 rounded border-gray-300 text-amber-600 focus:ring-amber-500">
                                <span class="ml-3 text-sm text-gray-600">I agree to the <a href="#" class="text-amber-600 hover:underline font-semibold">terms and conditions</a></span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="mt-6 w-full py-4 px-6 place-order-btn text-white font-bold text-lg rounded-xl shadow-lg">
                            <i class="fas fa-lock mr-3"></i>Place Order
                        </button>

                        <!-- Security Note -->
                        <div class="mt-4 flex items-center justify-center text-gray-500">
                            <i class="fas fa-shield-alt mr-2 text-green-500"></i>
                            <span class="text-sm">Secure checkout powered by PaSSSna</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let selectedPayment = 'cash';
    let selectedDelivery = 'takeaway';
    const currencySymbol = @json(config('restaurant.payment.currency_symbol', 'LKR '));

    function selectPayment(method) {
        // Remove active class from all payment methods
        document.querySelectorAll('.payment-method').forEach(pm => {
            pm.classList.remove('active');
        });

        // Add active class to selected method
        const selected = document.querySelector(`[onclick="selectPayment('${method}')"]`);
        selected.classList.add('active');

        // Show/hide card details
        const cardDetails = document.getElementById('cardDetails');
        if(method === 'card') {
            cardDetails.classList.remove('hidden');
            cardDetails.classList.add('animate-fade-in');
        } else {
            cardDetails.classList.add('hidden');
        }

        selectedPayment = method;
        document.getElementById('paymentMethod').value = method;

        // Update cash on delivery availability
        updatePaymentOptions();
    }

    function selectDelivery(type) {
        // Remove active class from all delivery options
        document.querySelectorAll('.delivery-option').forEach(option => {
            option.classList.remove('active');
        });

        // Add active class to selected option
        const selected = document.querySelector(`[onclick="selectDelivery('${type}')"]`);
        selected.classList.add('active');

        selectedDelivery = type;
        document.getElementById('deliveryType').value = type;

        // Show/hide delivery address
        const addressSection = document.getElementById('deliveryAddressSection');
        if(type === 'delivery') {
            addressSection.classList.remove('hidden');
            addressSection.classList.add('animate-slide-in');
        } else {
            addressSection.classList.add('hidden');
        }

        // Update estimated time
        updateEstimatedTime(type);

        // Update payment options
        updatePaymentOptions();

        // Update order summary (delivery fee and total)
        updateOrderSummary(type);
    }

    function updateEstimatedTime(type) {
        const timeElement = document.getElementById('estimatedTime');
        switch(type) {
            case 'delivery':
                timeElement.textContent = '30-45 minutes';
                break;
            case 'takeaway':
                timeElement.textContent = '15-20 minutes';
                break;
            case 'dine_in':
                timeElement.textContent = 'Immediate';
                break;
        }
    }

    function updatePaymentOptions() {
        // If delivery is selected, cash on delivery is available
        // Otherwise, only card payment is available
        if(selectedDelivery === 'delivery') {
            // Both cash and card are available
            document.querySelector('[onclick="selectPayment(\'cash\')"]').style.display = 'block';
            document.querySelector('[onclick="selectPayment(\'card\')"]').style.display = 'block';
        } else {
            // Only card payment for takeaway/dine-in
            document.querySelector('[onclick="selectPayment(\'cash\')"]').style.display = 'none';

            // If cash was selected, switch to card
            if(selectedPayment === 'cash') {
                selectPayment('card');
            }
        }
    }

    function updateOrderSummary(deliveryType) {
        // Get base values from the page
        const subtotal = {{ $subtotal }};
        const discount = {{ $discount ?? 0 }};
        const tax = {{ $tax }};
        const serviceCharge = {{ $serviceCharge ?? 0 }};
        const deliveryFee = {{ $deliveryFee }};
        const freeDeliveryThreshold = {{ $freeDeliveryThreshold ?? 0 }};

        const deliveryFeeDisplay = document.getElementById('deliveryFeeDisplay');
        const orderTotal = document.getElementById('orderTotal');
        const deliveryFeeRow = document.getElementById('deliveryFeeRow');

        // Calculate delivery charge based on delivery type and threshold
        let deliveryCharge = 0;
        if (deliveryType === 'delivery') {
            if (freeDeliveryThreshold > 0 && subtotal >= freeDeliveryThreshold) {
                deliveryCharge = 0;
                deliveryFeeDisplay.innerHTML = '<span class="text-green-600 font-bold"><i class="fas fa-gift mr-1"></i>Free</span>';
            } else {
                deliveryCharge = deliveryFee;
                deliveryFeeDisplay.textContent = currencySymbol + deliveryCharge.toFixed(2);
            }
        } else {
            deliveryCharge = 0;
            deliveryFeeDisplay.innerHTML = '<span class="text-gray-400">N/A</span>';
        }

        // Calculate total
        const total = subtotal - discount + deliveryCharge + tax + serviceCharge;
        orderTotal.textContent = currencySymbol + total.toFixed(2);
    }

    function toggleSchedule() {
        const scheduleCheckbox = document.getElementById('scheduleOrder');
        const scheduleOptions = document.getElementById('scheduleOptions');

        if(scheduleCheckbox.checked) {
            scheduleOptions.classList.remove('hidden');
            scheduleOptions.classList.add('animate-slide-in');

            // Set default date/time
            const now = new Date();
            const tomorrow = new Date(now);
            tomorrow.setDate(tomorrow.getDate() + 1);

            document.querySelector('input[name="schedule_date"]').value = tomorrow.toISOString().split('T')[0];
            document.querySelector('input[name="schedule_time"]').value = '18:00';
        } else {
            scheduleOptions.classList.add('hidden');
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Set default selections
        selectDelivery('takeaway');
        selectPayment('cash');

        // Format phone number input
        const phoneInput = document.querySelector('input[name="contact[phone]"]');
        phoneInput?.addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });

        // Format card number input
        const cardNumberInput = document.querySelector('input[name="card[number]"]');
        cardNumberInput?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            e.target.value = value.substring(0, 19);
        });

        // Format expiry date input
        const expiryInput = document.querySelector('input[name="card[expiry]"]');
        expiryInput?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if(value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value.substring(0, 5);
        });

        // Format CVV input
        const cvvInput = document.querySelector('input[name="card[cvv]"]');
        cvvInput?.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
        });
    });

    // Form validation
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const terms = document.getElementById('terms');
        if(!terms.checked) {
            e.preventDefault();
            showToast('Please agree to the terms and conditions', 'error');
            return false;
        }

        if(selectedPayment === 'card') {
            // Simple card validation (in real app, use proper validation)
            const cardNumber = document.querySelector('input[name="card[number]"]').value;
            const expiry = document.querySelector('input[name="card[expiry]"]').value;
            const cvv = document.querySelector('input[name="card[cvv]"]').value;
            const name = document.querySelector('input[name="card[name]"]').value;

            if(!cardNumber || !expiry || !cvv || !name) {
                e.preventDefault();
                showToast('Please fill in all card details', 'error');
                return false;
            }
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i>Processing...';
        submitBtn.disabled = true;
    });
</script>
@endsection


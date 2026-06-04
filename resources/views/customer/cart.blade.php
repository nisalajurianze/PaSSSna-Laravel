@extends('layouts.app')

@section('title', 'Shopping Cart - PaSSSna Restaurant')

@section('styles')
<style>
    .cart-item {
        transition: all 0.3s ease;
    }
    .cart-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .quantity-btn {
        transition: all 0.2s ease;
    }
    .quantity-btn:hover {
        transform: scale(1.1);
    }
    .quantity-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-12">
    <h1 class="text-4xl font-bold text-gray-800 mb-8 animate-fade-in">Your Shopping Cart</h1>

    @if(empty($cart) || count($cart) == 0)
    <div class="bg-gradient-to-r from-yellow-50 to-red-50 rounded-xl p-8 text-center animate-slide-up">
        <div class="w-24 h-24 bg-gradient-to-r from-primary-red to-primary-yellow rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-shopping-cart text-white text-3xl"></i>
        </div>
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Your cart is empty</h2>
        <p class="text-gray-600 mb-6">Add some delicious items from our menu to get started!</p>
        <a href="{{ route('menu') }}" class="bg-primary-red text-white px-6 py-3 rounded-lg hover:bg-red-700 transition duration-300 inline-flex items-center">
            <i class="fas fa-utensils mr-2"></i>Browse Menu
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
            <!-- Cart Header -->
            <div class="bg-gray-100 rounded-lg p-4 mb-4 hidden md:grid grid-cols-12 gap-4">
                <div class="col-span-5 font-semibold text-gray-700">Item</div>
                <div class="col-span-2 font-semibold text-gray-700 text-center">Price</div>
                <div class="col-span-3 font-semibold text-gray-700 text-center">Quantity</div>
                <div class="col-span-2 font-semibold text-gray-700 text-right">Total</div>
            </div>

            <!-- Cart Items List -->
            <div class="space-y-4" id="cartItemsContainer">
                @foreach($cart as $key => $item)
                <div class="cart-item bg-white rounded-xl shadow-md p-4 animate-slide-up cart-item-row" id="cart-item-{{ $key }}" style="animation-delay: {{ $loop->index * 0.05 }}s">
                    <div class="flex flex-col md:flex-row md:items-center gap-4">
                        <!-- Item Image & Info -->
                        <div class="flex items-center space-x-4 md:w-5/12">
                            <div class="w-20 h-20 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                @if($item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full bg-gradient-to-r from-gray-300 to-gray-400 flex items-center justify-center">
                                    <i class="fas fa-utensils text-gray-600 text-xl"></i>
                                </div>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">{{ $item['name'] }}</h3>
                                @if($item['size'])
                                <p class="text-sm text-gray-600">Size: <span class="capitalize">{{ $item['size'] }}</span></p>
                                @endif
                                @if(!empty($item['toppings']))
                                <p class="text-sm text-gray-600">Toppings: {{ implode(', ', $item['toppings']) }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="md:w-2/12 text-center">
                            <span class="text-lg font-semibold text-gray-800 item-price">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item['price'], 2) }}</span>
                        </div>

                        <!-- Quantity Controls -->
                        <div class="md:w-3/12">
                            <div class="flex items-center justify-center space-x-3">
                                <button onclick="updateQuantity('{{ $key }}', -1)"
                                        class="quantity-btn w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center"
                                        id="btn-decrease-{{ $key }}">
                                    <i class="fas fa-minus text-gray-700"></i>
                                </button>
                                <span class="text-lg font-semibold w-8 text-center item-quantity" id="qty-{{ $key }}">{{ $item['quantity'] }}</span>
                                <button onclick="updateQuantity('{{ $key }}', 1)"
                                        class="quantity-btn w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center"
                                        id="btn-increase-{{ $key }}">
                                    <i class="fas fa-plus text-gray-700"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Total & Remove -->
                        <div class="md:w-2/12 flex items-center justify-between md:justify-end">
                            <span class="text-lg font-bold text-primary-red item-total" id="total-{{ $key }}">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item['total'], 2) }}</span>
                            <button onclick="removeItem('{{ $key }}')"
                                    class="ml-4 text-red-500 hover:text-red-700 transition duration-300">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Clear Cart & Continue Shopping -->
            <div class="flex flex-wrap justify-between items-center mt-8">
                <button onclick="clearCart()"
                        class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition duration-300 mb-4 md:mb-0">
                    <i class="fas fa-trash-alt mr-2"></i>Clear Cart
                </button>
                <a href="{{ route('menu') }}"
                   class="bg-primary-yellow text-gray-800 px-6 py-3 rounded-lg hover:bg-yellow-500 transition duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                </a>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6 sticky top-24">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-4 border-b">Order Summary</h2>

                <!-- Subtotal -->
                <div class="flex justify-between mb-3">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-semibold" id="summary-subtotal">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($subtotal, 2) }}</span>
                </div>

                <!-- Tax -->
                <div class="flex justify-between mb-3">
                    <span class="text-gray-600">Tax ({{ config('restaurant.order.tax_rate', 8) }}%)</span>
                    <span class="font-semibold" id="summary-tax">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($tax, 2) }}</span>
                </div>

                <!-- Service Charge -->
                <div class="flex justify-between mb-3">
                    <span class="text-gray-600">Service Charge ({{ config('restaurant.order.service_charge_rate', 10) }}%)</span>
                    <span class="font-semibold" id="summary-service">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format(($subtotal - $discount) * 0.10, 2) }}</span>
                </div>

                <!-- Promo Code -->
                <div class="mb-6">
                    <div class="flex mb-2">
                        <input type="text"
                               id="promoCode"
                               placeholder="Enter promo code"
                               class="flex-grow border border-gray-300 rounded-l-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-primary-red">
                        <button onclick="applyPromoCode()"
                                class="bg-gray-800 text-white px-4 py-2 rounded-r-lg hover:bg-gray-900 transition duration-300">
                            Apply
                        </button>
                    </div>
                    @if($discount > 0)
                    <div class="text-green-600 text-sm flex justify-between items-center" id="promoApplied">
                        <span>Promo code applied: -{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($discount, 2) }}</span>
                        <button onclick="removePromoCode()" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Total -->
                <div class="flex justify-between items-center mb-6 pt-4 border-t">
                    <span class="text-xl font-bold text-gray-800">Total</span>
                    <span class="text-2xl font-bold text-primary-red" id="summary-total">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($total, 2) }}</span>
                </div>

                <!-- Checkout Button -->
                <a href="{{ route('checkout') }}"
                   class="block w-full bg-gradient-to-r from-primary-red to-primary-yellow text-white text-center py-4 rounded-lg font-semibold hover:opacity-90 transition duration-300 mb-4">
                    <i class="fas fa-shopping-bag mr-2"></i>Proceed to Checkout
                </a>

                <!-- Payment Methods -->
                <div class="text-center text-gray-600 text-sm">
                    <p class="mb-2">We accept:</p>
                    <div class="flex justify-center space-x-4">
                        <i class="fab fa-cc-visa text-2xl text-blue-600"></i>
                        <i class="fab fa-cc-mastercard text-2xl text-red-600"></i>
                        <i class="fab fa-cc-amex text-2xl text-blue-800"></i>
                        <i class="fab fa-cc-paypal text-2xl text-blue-500"></i>
                    </div>
                </div>

                <!-- Delivery Info -->
                <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-yellow-50 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-shipping-fast text-primary-red text-xl mr-3 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-gray-800">Delivery Information</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                Estimated delivery time: <span class="font-semibold">30-45 minutes</span><br>
                                Free delivery on orders over {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format(config('restaurant.order.free_delivery_threshold', 0), 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Version for cache busting
    const CART_VERSION = Date.now();

    // Store cart update URL base for JavaScript
    const cartUpdateUrlBase = '{{ url('/cart/update-quantity') }}?_v=' + CART_VERSION;
    const cartRemoveUrlBase = '{{ url('/cart/remove') }}?_v=' + CART_VERSION;
    const cartClearUrl = '{{ route('cart.clear') }}?_v=' + CART_VERSION;
    const cartSummaryUrl = '{{ route('cart.getSummary') }}?_v=' + CART_VERSION;

    const taxRate = {{ config('restaurant.order.tax_rate', 8) }};
    const serviceChargeRate = {{ config('restaurant.order.service_charge_rate', 10) }};
    const currencySymbol = @json(config('restaurant.payment.currency_symbol', 'LKR '));

    function updateQuantity(key, change) {
        const btnDecrease = document.getElementById('btn-decrease-' + key);
        const btnIncrease = document.getElementById('btn-increase-' + key);

        // Disable buttons during request
        if (btnDecrease) btnDecrease.disabled = true;
        if (btnIncrease) btnIncrease.disabled = true;

        fetch(cartUpdateUrlBase.split('?')[0] + '/' + key + '?quantity_change=' + change, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Update response status:', response.status);
            if (!response.ok) throw new Error('Network response was not ok: ' + response.status);
            return response.json();
        })
        .then(data => {
            console.log('Update response data:', data);
            if(data.success) {
                if (data.removed) {
                    // Item was removed, remove from DOM
                    const itemRow = document.getElementById('cart-item-' + key);
                    if (itemRow) {
                        itemRow.style.transition = 'all 0.3s ease';
                        itemRow.style.opacity = '0';
                        itemRow.style.transform = 'translateX(-20px)';
                        setTimeout(() => {
                            itemRow.remove();
                            checkEmptyCart();
                        }, 300);
                    }
                } else {
                    // Update quantity display
                    const qtyElement = document.getElementById('qty-' + key);
                    if (qtyElement && data.item) {
                        qtyElement.textContent = data.item.quantity;

                        // Update item total
                        const totalElement = document.getElementById('total-' + key);
                        if (totalElement) {
                            totalElement.textContent = currencySymbol + data.item.total.toFixed(2);
                        }
                    }
                }

                // Update summary
                updateCartSummary();
                updateCartBadge(data.cart_count);
                showToast('Cart updated!', 'success');
            } else {
                showToast(data.message || 'Error updating cart', 'error');
            }
        })
        .catch(error => {
            console.error('Update error:', error);
            showToast('Error updating cart: ' + error.message, 'error');
        })
        .finally(() => {
            // Re-enable buttons
            if (btnDecrease) btnDecrease.disabled = false;
            if (btnIncrease) btnIncrease.disabled = false;
        });
    }

    function removeItem(key) {
        Swal.fire({
            title: 'Remove Item?',
            text: "Are you sure you want to remove this item from your cart?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Removing...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const url = cartRemoveUrlBase.split('?')[0] + '/' + key;
                console.log('Remove URL:', url);

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        _method: 'DELETE'
                    })
                })
                .then(response => {
                    console.log('Remove response status:', response.status);
                    if (!response.ok) throw new Error('Network response was not ok: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Remove response data:', data);
                    if(data.success) {
                        Swal.close();

                        // Animate removal
                        const itemRow = document.getElementById('cart-item-' + key);
                        if (itemRow) {
                            itemRow.style.transition = 'all 0.3s ease';
                            itemRow.style.opacity = '0';
                            itemRow.style.transform = 'translateX(-20px)';
                            setTimeout(() => {
                                itemRow.remove();
                                checkEmptyCart();
                            }, 300);
                        }

                        updateCartSummary();
                        updateCartBadge(data.cart_count);
                        showToast('Item removed from cart', 'success');
                    } else {
                        Swal.fire('Error!', data.message || 'Failed to remove item', 'error');
                    }
                })
                .catch(error => {
                    console.error('Remove error:', error);
                    // Even on error, try to remove the item from UI
                    const itemRow = document.getElementById('cart-item-' + key);
                    if (itemRow) {
                        itemRow.remove();
                        checkEmptyCart();
                    }
                    updateCartBadge(0);
                    Swal.close();
                    showToast('Item removed (offline)', 'success');
                });
            }
        });
    }

    function clearCart() {
        Swal.fire({
            title: 'Clear Entire Cart?',
            text: "This will remove all items from your cart. This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, clear cart!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Clearing cart...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                console.log('Clear cart URL:', cartClearUrl);

                fetch(cartClearUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    console.log('Clear cart response status:', response.status);
                    if (!response.ok) throw new Error('Network response was not ok: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Clear cart response data:', data);
                    if(data.success) {
                        Swal.close();
                        doClearCartUI();
                        showToast('Cart cleared successfully', 'success');
                    } else {
                        Swal.fire('Error!', data.message || 'Failed to clear cart', 'error');
                    }
                })
                .catch(error => {
                    console.error('Clear cart error:', error);
                    // Even on error, try to clear the cart UI
                    doClearCartUI();
                    Swal.close();
                    showToast('Cart cleared (offline)', 'success');
                });
            }
        });
    }

    function doClearCartUI() {
        // Animate all items out
        const cartItems = document.querySelectorAll('.cart-item-row');
        cartItems.forEach((item, index) => {
            setTimeout(() => {
                item.style.transition = 'all 0.3s ease';
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
            }, index * 100);
        });

        // Clear cart and update UI
        setTimeout(() => {
            updateCartBadge(0);

            // Update summary to zeros
            const subtotalEl = document.getElementById('summary-subtotal');
            const taxEl = document.getElementById('summary-tax');
            const serviceEl = document.getElementById('summary-service');
            const totalEl = document.getElementById('summary-total');

            if (subtotalEl) subtotalEl.textContent = currencySymbol + '0.00';
            if (taxEl) taxEl.textContent = currencySymbol + '0.00';
            if (serviceEl) serviceEl.textContent = currencySymbol + '0.00';
            if (totalEl) totalEl.textContent = currencySymbol + '0.00';

            // Remove all item rows from DOM
            cartItems.forEach(item => item.remove());

            // Check if cart is empty and show empty state
            checkEmptyCart();
        }, cartItems.length * 100 + 300);
    }

    function applyPromoCode() {
        const promoCode = document.getElementById('promoCode').value;

        if(!promoCode) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops!',
                text: 'Please enter a promo code',
            });
            return;
        }

        fetch('{{ route('cart.applyPromo') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                promo_code: promoCode
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                updateCartSummary();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Code',
                    text: data.message,
                });
            }
        });
    }

    function removePromoCode() {
        fetch('{{ route('cart.removePromo') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                updateCartSummary();
            }
        });
    }

    function updateCartSummary() {
        fetch(cartSummaryUrl)
        .then(response => response.json())
        .then(data => {
            // Update subtotal
            const subtotalElement = document.getElementById('summary-subtotal');
            if (subtotalElement) {
                subtotalElement.textContent = currencySymbol + data.subtotal.toFixed(2);
            }

            // Update tax
            const taxElement = document.getElementById('summary-tax');
            if (taxElement) {
                taxElement.textContent = currencySymbol + data.tax_amount.toFixed(2);
            }

            // Update service charge
            const serviceElement = document.getElementById('summary-service');
            if (serviceElement) {
                serviceElement.textContent = currencySymbol + data.service_charge.toFixed(2);
            }

            // Update total
            const totalElement = document.getElementById('summary-total');
            if (totalElement) {
                totalElement.textContent = currencySymbol + data.total.toFixed(2);
            }
        });
    }

    function updateCartBadge(count) {
        // Update badge in header
        const cartLink = document.querySelector('a[href="{{ route('cart') }}"]');
        if (cartLink) {
            let badge = cartLink.querySelector('span');
            if (!badge && count > 0) {
                badge = document.createElement('span');
                badge.className = 'absolute -top-1 -right-1 bg-primary-red text-white text-xs rounded-full w-5 h-5 flex items-center justify-center animate-pulse-slow';
                cartLink.appendChild(badge);
            }
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        }
    }

    function checkEmptyCart() {
        const remainingItems = document.querySelectorAll('.cart-item-row');
        if (remainingItems.length === 0) {
            setTimeout(() => {
                window.location.reload();
            }, 500);
        }
    }
</script>
@endsection


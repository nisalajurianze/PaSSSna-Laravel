@extends('layouts.app')

@section('title', 'Order Confirmation - PaSSSna Restaurant')

@section('styles')
<style>
    .confirmation-animation {
        animation: celebrate 2s ease-in-out infinite;
    }
    @keyframes celebrate {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    .tracking-step {
        position: relative;
        padding-left: 30px;
        margin-bottom: 20px;
    }
    .tracking-step::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #E5E7EB;
    }
    .tracking-step.active::before {
        background: #10B981;
        box-shadow: 0 0 0 5px rgba(16, 185, 129, 0.2);
    }
    .tracking-step.completed::before {
        background: #10B981;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gradient-to-b from-yellow-50 to-red-50 py-12">
    <div class="container mx-auto px-4">
        <!-- Success Animation -->
        <div class="max-w-2xl mx-auto text-center mb-12">
            <div class="confirmation-animation w-32 h-32 bg-gradient-to-r from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-white text-5xl"></i>
            </div>
            <h1 class="text-5xl font-bold text-gray-800 mb-4">Order Confirmed!</h1>
            <p class="text-xl text-gray-600">Thank you for your order, {{ $order->user->name }}!</p>
        </div>

        <!-- Order Details Card -->
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <!-- Order Header -->
            <div class="bg-gradient-to-r from-primary-red to-primary-yellow p-8 text-white">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold">Order #{{ $order->order_number }}</h2>
                        <p class="opacity-90">{{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <span class="inline-block px-6 py-2 bg-white text-primary-red rounded-full font-semibold text-lg">
                            {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->total, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Order Status & Tracking -->
            <div class="p-8 border-b">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Order Status</h3>
                <div class="relative">
                    <!-- Progress Bar -->
                    <div class="absolute top-3 left-0 w-full h-1 bg-gray-200"></div>
                    <div class="absolute top-3 left-0 h-1 bg-green-500" style="width: {{ $order->getProgressPercentage() }}%"></div>

                    <!-- Steps -->
                    <div class="flex justify-between relative z-10">
                        @foreach(['pending', 'confirmed', 'preparing', 'ready', 'delivered'] as $status)
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center mb-2
                                {{ $order->status == $status ? 'bg-green-500 text-white' : (($order->getStatusIndex($status) <= $order->getStatusIndex($order->status)) ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-600') }}">
                                @switch($status)
                                    @case('pending')<i class="fas fa-clock"></i>@break
                                    @case('confirmed')<i class="fas fa-check-circle"></i>@break
                                    @case('preparing')<i class="fas fa-utensils"></i>@break
                                    @case('ready')<i class="fas fa-box"></i>@break
                                    @case('delivered')<i class="fas fa-home"></i>@break
                                @endswitch
                            </div>
                            <span class="text-sm font-semibold capitalize">{{ $status }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Current Status -->
                <div class="mt-8 p-4 bg-gradient-to-r from-blue-50 to-green-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mr-4">
                            <i class="fas fa-info-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Current Status:
                                <span class="capitalize text-primary-red">{{ $order->status }}</span>
                            </h4>
                            <p class="text-gray-600">
                                @switch($order->status)
                                    @case('pending')Your order has been received and is being processed.@break
                                    @case('confirmed')Your order has been confirmed and will be prepared soon.@break
                                    @case('preparing')Our chefs are preparing your delicious meal.@break
                                    @case('ready')Your order is ready for pickup/delivery.@break
                                    @case('delivered')Enjoy your meal!@break
                                @endswitch
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="p-8">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Order Details</h3>

                <!-- Items -->
                <div class="mb-8">
                    <h4 class="font-semibold text-gray-700 mb-4">Items Ordered</h4>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden mr-4">
                                    @if($item->menuItem->image)
                                    <img src="{{ asset('storage/' . $item->menuItem->image) }}" alt="{{ $item->menuItem->name }}" class="w-full h-full object-cover">
                                    @else
                                    <div class="w-full h-full bg-gradient-to-r from-gray-300 to-gray-400 flex items-center justify-center">
                                        <i class="fas fa-utensils text-gray-600"></i>
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="font-semibold text-gray-800">{{ $item->menuItem->name }}</h5>
                                    <p class="text-sm text-gray-600">
                                        Qty: {{ $item->quantity }}
                                        @if($item->size)
                                        • Size: {{ ucfirst($item->size) }}
                                        @endif
                                    </p>
                                    @if(!empty($item->toppings))
                                    <p class="text-sm text-gray-600">Toppings: {{ implode(', ', json_decode($item->toppings, true)) }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="font-semibold">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Summary -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="font-semibold text-gray-700 mb-4">Order Summary</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tax</span>
                            <span class="font-semibold">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->tax, 2) }}</span>
                        </div>
                        @if($order->delivery_charge > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Delivery Charge</span>
                            <span class="font-semibold">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->delivery_charge, 2) }}</span>
                        </div>
                        @endif
                        @if($order->discount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Discount</span>
                            <span class="font-semibold">-{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->discount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between pt-4 border-t">
                            <span class="text-xl font-bold text-gray-800">Total</span>
                            <span class="text-2xl font-bold text-primary-red">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Delivery/Pickup Info -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6">
                        <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            @if($order->order_type == 'delivery')
                            Delivery Information
                            @elseif($order->order_type == 'takeaway')
                            Pickup Information
                            @else
                            Dine-in Information
                            @endif
                        </h4>

                        @if($order->order_type == 'delivery')
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Delivery Address</p>
                                <p class="font-semibold">{{ $order->delivery_address }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Estimated Delivery</p>
                                <p class="font-semibold text-primary-red">
                                    {{ $order->estimated_delivery_time->format('h:i A') }}
                                    ({{ $order->estimated_delivery_time->diffForHumans() }})
                                </p>
                            </div>
                            @if($order->special_instructions)
                            <div>
                                <p class="text-sm text-gray-600">Special Instructions</p>
                                <p class="font-semibold">{{ $order->special_instructions }}</p>
                            </div>
                            @endif
                        </div>
                        @elseif($order->order_type == 'takeaway')
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Pickup Location</p>
                                <p class="font-semibold">PaSSSna Restaurant, 123 Gourmet Street</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Estimated Ready Time</p>
                                <p class="font-semibold text-primary-red">
                                    {{ $order->estimated_delivery_time->format('h:i A') }}
                                    ({{ $order->estimated_delivery_time->diffForHumans() }})
                                </p>
                            </div>
                        </div>
                        @else
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Table Number</p>
                                <p class="font-semibold">Table {{ $order->table_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Estimated Serving Time</p>
                                <p class="font-semibold text-primary-red">
                                    {{ $order->estimated_delivery_time->format('h:i A') }}
                                    ({{ $order->estimated_delivery_time->diffForHumans() }})
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Payment Information -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-6">
                        <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-credit-card text-green-600 mr-2"></i>
                            Payment Information
                        </h4>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Payment Method</p>
                                <p class="font-semibold capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Payment Status</p>
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                    {{ $order->payment_status == 'completed' ? 'bg-green-100 text-green-800' : (($order->payment_status == 'pending') ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Order ID</p>
                                <p class="font-semibold font-mono">{{ $order->order_number }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="max-w-4xl mx-auto flex flex-wrap justify-center gap-4">
            <a href="{{ route('pdf.order', $order->id) }}"
               target="_blank"
               class="bg-gray-800 text-white px-8 py-4 rounded-lg hover:bg-gray-900 transition duration-300 flex items-center">
                <i class="fas fa-file-pdf mr-2"></i>Download Invoice (PDF)
            </a>
            <a href="{{ route('customer.orders') }}"
               class="bg-primary-yellow text-gray-800 px-8 py-4 rounded-lg hover:bg-yellow-500 transition duration-300 flex items-center">
                <i class="fas fa-history mr-2"></i>View Order History
            </a>
            <a href="{{ route('menu') }}"
               class="bg-primary-red text-white px-8 py-4 rounded-lg hover:bg-red-700 transition duration-300 flex items-center">
                <i class="fas fa-utensils mr-2"></i>Order More Food
            </a>
            <button onclick="shareOrder()"
                    class="bg-blue-600 text-white px-8 py-4 rounded-lg hover:bg-blue-700 transition duration-300 flex items-center">
                <i class="fas fa-share-alt mr-2"></i>Share Order
            </button>
        </div>

        <!-- Next Steps -->
        <div class="max-w-2xl mx-auto mt-12 p-8 bg-white rounded-2xl shadow-lg">
            <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">What's Next?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-6 bg-gradient-to-b from-blue-50 to-white rounded-xl">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-bell text-blue-600 text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-800 mb-2">Get Notified</h4>
                    <p class="text-sm text-gray-600">We'll send you updates about your order status via SMS and email.</p>
                </div>
                <div class="text-center p-6 bg-gradient-to-b from-yellow-50 to-white rounded-xl">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-800 mb-2">Track Your Order</h4>
                    <p class="text-sm text-gray-600">Use your order number to track real-time progress in your dashboard.</p>
                </div>
                <div class="text-center p-6 bg-gradient-to-b from-green-50 to-white rounded-xl">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-green-600 text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-800 mb-2">Need Help?</h4>
                    <p class="text-sm text-gray-600">Contact our support team at <strong>+1 (555) 123-4567</strong> for assistance.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const currencySymbol = @json(config('restaurant.payment.currency_symbol', 'LKR '));

    function shareOrder() {
        if (navigator.share) {
            navigator.share({
                title: 'My PaSSSna Order',
                text: `I just ordered from PaSSSna Restaurant! Order #${@json($order->order_number)}`,
                url: window.location.href,
            })
            .then(() => console.log('Successful share'))
            .catch((error) => console.log('Error sharing:', error));
        } else {
            // Fallback: Copy to clipboard
            const orderDetails = `Order #${@json($order->order_number)}\nTotal: ${currencySymbol}${@json(number_format($order->total, 2))}\nStatus: ${@json($order->status)}`;
            navigator.clipboard.writeText(orderDetails).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Order details copied to clipboard',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        }
    }

    // Auto-refresh page every 30 seconds for order updates
    setTimeout(() => {
        location.reload();
    }, 30000);
</script>
@endsection


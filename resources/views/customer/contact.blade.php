@extends('layouts.app')

@section('title', 'Contact Us - PaSSSna Restaurant')

@section('styles')
<style>
    .contact-tab {
        transition: all 0.3s ease;
    }
    .contact-tab:hover {
        transform: translateY(-2px);
    }
    .contact-tab.active {
        background: linear-gradient(135deg, #DC2626, #FBBF24);
        color: white;
        transform: translateY(-2px);
    }
    .faq-item {
        transition: all 0.3s ease;
    }
    .faq-item:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .review-card {
        animation: slideInRight 0.5s ease-out;
    }
    @keyframes slideInRight {
        from { transform: translateX(30px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-6xl mx-auto">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <img src="{{ asset('PASSSNA.png') }}" alt="PaSSSna Logo" class="h-32 w-auto mx-auto mb-6 animate-fade-in">
            <h1 class="text-5xl font-bold text-gray-800 mb-4 animate-fade-in">Contact Us</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">We'd love to hear from you. Reach out for reservations, feedback, or any inquiries.</p>
        </div>

        <!-- Contact Tabs -->
        <div class="mb-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button data-tab="contact-form"
                        class="contact-tab active bg-white rounded-xl shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-primary-red to-primary-yellow rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-envelope text-white text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">Send Message</h3>
                </button>
                <button data-tab="contact-info"
                        class="contact-tab bg-white rounded-xl shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-info-circle text-white text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">Contact Info</h3>
                </button>
                <button data-tab="location"
                        class="contact-tab bg-white rounded-xl shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-green-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map-marker-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">Location</h3>
                </button>
                <button data-tab="faq"
                        class="contact-tab bg-white rounded-xl shadow-md p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-purple-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-question-circle text-white text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">FAQ</h3>
                </button>
            </div>
        </div>

        <!-- Contact Form Tab -->
        <div id="contact-form" class="tab-content active">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div class="animate-slide-up">
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <h2 class="text-3xl font-bold text-gray-800 mb-6">Send Us a Message</h2>
                        <form id="contactForm" action="{{ route('contact.store') }}" method="POST">
                            @csrf

                            <div class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-gray-700 mb-2 font-semibold">Your Name *</label>
                                        <input type="text"
                                               name="name"
                                               value="{{ auth()->check() ? auth()->user()->name : '' }}"
                                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-red"
                                               required>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 mb-2 font-semibold">Email Address *</label>
                                        <input type="email"
                                               name="email"
                                               value="{{ auth()->check() ? auth()->user()->email : '' }}"
                                               class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-red"
                                               required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-gray-700 mb-2 font-semibold">Phone Number</label>
                                    <input type="tel"
                                           name="phone"
                                           value="{{ auth()->check() ? auth()->user()->phone : '' }}"
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-red">
                                </div>

                                <div>
                                    <label class="block text-gray-700 mb-2 font-semibold">Subject *</label>
                                    <select name="subject"
                                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-red"
                                            required>
                                        <option value="">Select a subject</option>
                                        <option value="reservation">Reservation Inquiry</option>
                                        <option value="feedback">Feedback & Suggestions</option>
                                        <option value="complaint">Complaint</option>
                                        <option value="catering">Catering Services</option>
                                        <option value="partnership">Business Partnership</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-gray-700 mb-2 font-semibold">Your Message *</label>
                                    <textarea name="message"
                                              rows="6"
                                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-red"
                                              placeholder="Please provide details about your inquiry..."
                                              required></textarea>
                                </div>

                                <button type="submit"
                                        class="w-full bg-gradient-to-r from-primary-red to-primary-yellow text-white py-4 rounded-lg font-semibold hover:opacity-90 transition duration-300">
                                    <i class="fas fa-paper-plane mr-2"></i>Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Response Time & Info -->
                <div class="animate-slide-up" style="animation-delay: 0.1s">
                    <div class="space-y-6">
                        <!-- Response Time -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-8">
                            <div class="flex items-center mb-6">
                                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                    <i class="fas fa-clock text-blue-600 text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800">Response Time</h3>
                                    <p class="text-gray-600">We typically respond within 2-4 hours</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                    <span class="font-medium text-gray-700">Email Inquiries</span>
                                    <span class="font-semibold text-blue-600">Within 4 hours</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                    <span class="font-medium text-gray-700">Phone Calls</span>
                                    <span class="font-semibold text-blue-600">Immediate</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                                    <span class="font-medium text-gray-700">Reservations</span>
                                    <span class="font-semibold text-blue-600">Within 1 hour</span>
                                </div>
                            </div>
                        </div>

                        <!-- Alternative Contact -->
                        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-2xl p-8">
                            <h3 class="text-xl font-bold text-gray-800 mb-6">Prefer to Call?</h3>
                            <div class="space-y-4">
                                <a href="tel:+15551234567"
                                   class="flex items-center justify-center bg-primary-red text-white px-6 py-4 rounded-lg hover:bg-red-700 transition duration-300">
                                    <i class="fas fa-phone-alt mr-3"></i>
                                    Call: +1 (555) 123-4567
                                </a>
                                <div class="text-center text-gray-600">
                                    <p>Monday - Sunday: 9:00 AM - 11:00 PM</p>
                                    <p class="text-sm mt-1">For urgent matters, calling is the fastest option</p>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl p-8">
                            <h3 class="text-xl font-bold text-gray-800 mb-6">Connect With Us</h3>
                            <div class="flex justify-center space-x-6">
                                <a href="#" class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center hover:bg-blue-700 transition duration-300">
                                    <i class="fab fa-facebook-f text-white text-xl"></i>
                                </a>
                                <a href="#" class="w-12 h-12 bg-blue-400 rounded-full flex items-center justify-center hover:bg-blue-500 transition duration-300">
                                    <i class="fab fa-twitter text-white text-xl"></i>
                                </a>
                                <a href="#" class="w-12 h-12 bg-pink-600 rounded-full flex items-center justify-center hover:bg-pink-700 transition duration-300">
                                    <i class="fab fa-instagram text-white text-xl"></i>
                                </a>
                                <a href="#" class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center hover:bg-red-600 transition duration-300">
                                    <i class="fab fa-youtube text-white text-xl"></i>
                                </a>
                            </div>
                            <p class="text-center text-gray-600 mt-4">
                                Follow us for updates, offers, and behind-the-scenes content
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Info Tab -->
        <div id="contact-info" class="tab-content hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Phone -->
                <div class="bg-white rounded-2xl shadow-xl p-8 text-center animate-slide-up">
                    <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-blue-700 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-phone-alt text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Phone</h3>
                    <p class="text-gray-600 mb-4">Call us for immediate assistance</p>
                    <a href="tel:+15551234567" class="text-2xl font-bold text-primary-red hover:text-red-700 transition duration-300">
                        +1 (555) 123-4567
                    </a>
                    <p class="text-sm text-gray-500 mt-2">Available 9 AM - 11 PM Daily</p>
                </div>

                <!-- Email -->
                <div class="bg-white rounded-2xl shadow-xl p-8 text-center animate-slide-up" style="animation-delay: 0.1s">
                    <div class="w-20 h-20 bg-gradient-to-r from-green-500 to-green-700 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-envelope text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Email</h3>
                    <p class="text-gray-600 mb-4">For detailed inquiries and feedback</p>
                    <a href="mailto:info@passsna.com" class="text-xl font-semibold text-primary-red hover:text-red-700 transition duration-300">
                        info@passsna.com
                    </a>
                    <p class="text-sm text-gray-500 mt-2">Response within 4 hours</p>
                </div>

                <!-- Reservations -->
                <div class="bg-white rounded-2xl shadow-xl p-8 text-center animate-slide-up" style="animation-delay: 0.2s">
                    <div class="w-20 h-20 bg-gradient-to-r from-primary-red to-primary-yellow rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-calendar-alt text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Reservations</h3>
                    <p class="text-gray-600 mb-4">For table bookings and events</p>
                    <a href="mailto:reservations@passsna.com" class="text-xl font-semibold text-primary-red hover:text-red-700 transition duration-300">
                        reservations@passsna.com
                    </a>
                    <p class="text-sm text-gray-500 mt-2">Or book online 24/7</p>
                </div>

                <!-- Catering -->
                <div class="bg-white rounded-2xl shadow-xl p-8 text-center animate-slide-up" style="animation-delay: 0.3s">
                    <div class="w-20 h-20 bg-gradient-to-r from-purple-500 to-purple-700 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-utensils text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">Catering</h3>
                    <p class="text-gray-600 mb-4">For events and special occasions</p>
                    <a href="mailto:catering@passsna.com" class="text-xl font-semibold text-primary-red hover:text-red-700 transition duration-300">
                        catering@passsna.com
                    </a>
                    <p class="text-sm text-gray-500 mt-2">Custom menus available</p>
                </div>
            </div>
        </div>

        <!-- Location Tab -->
        <div id="location" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Map -->
                <div class="animate-slide-up">
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="h-96 bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                            <!-- This would be a real Google Maps embed in production -->
                            <div class="text-center">
                                <i class="fas fa-map-marked-alt text-gray-600 text-6xl mb-4"></i>
                                <h3 class="text-2xl font-bold text-gray-800">PaSSSna Restaurant</h3>
                                <p class="text-gray-600">Interactive map would display here</p>
                            </div>
                        </div>
                        <div class="p-8">
                            <h3 class="text-2xl font-bold text-gray-800 mb-4">Our Location</h3>
                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <i class="fas fa-map-marker-alt text-primary-red text-xl mt-1 mr-3"></i>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Address</h4>
                                        <p class="text-gray-600">123 Gourmet Street, Food City, FC 12345</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-directions text-primary-red text-xl mt-1 mr-3"></i>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Getting Here</h4>
                                        <p class="text-gray-600">Free parking available • 5 min walk from subway station</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <i class="fas fa-car text-primary-red text-xl mt-1 mr-3"></i>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">Parking</h4>
                                        <p class="text-gray-600">Complimentary valet parking • 50+ parking spaces</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hours & Nearby -->
                <div class="space-y-8 animate-slide-up" style="animation-delay: 0.1s">
                    <!-- Hours -->
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">Opening Hours</h3>
                        <div class="space-y-4">
                            @foreach([
                                ['day' => 'Monday - Thursday', 'hours' => '11:00 AM - 10:00 PM', 'kitchen' => '10:30 PM'],
                                ['day' => 'Friday - Saturday', 'hours' => '11:00 AM - 11:00 PM', 'kitchen' => '10:30 PM'],
                                ['day' => 'Sunday', 'hours' => '12:00 PM - 9:00 PM', 'kitchen' => '8:30 PM'],
                                ['day' => 'Holidays', 'hours' => '12:00 PM - 8:00 PM', 'kitchen' => '7:30 PM']
                            ] as $schedule)
                            <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                                <div>
                                    <h4 class="font-semibold text-gray-800">{{ $schedule['day'] }}</h4>
                                    <p class="text-sm text-gray-500">Last orders: {{ $schedule['kitchen'] }}</p>
                                </div>
                                <span class="font-bold text-gray-800">{{ $schedule['hours'] }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-6 p-4 bg-gradient-to-r from-yellow-50 to-red-50 rounded-lg">
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-info-circle text-primary-red mr-2"></i>
                                Extended hours available for private events. Contact us for arrangements.
                            </p>
                        </div>
                    </div>

                    <!-- Nearby Attractions -->
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6">Nearby Attractions</h3>
                        <div class="space-y-4">
                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                <i class="fas fa-film text-purple-600 text-xl mr-4"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-800">City Cinema</h4>
                                    <p class="text-sm text-gray-600">5-minute walk • Perfect for dinner & movie</p>
                                </div>
                            </div>
                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                <i class="fas fa-shopping-bag text-blue-600 text-xl mr-4"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-800">Metro Mall</h4>
                                    <p class="text-sm text-gray-600">10-minute drive • Shopping & dining</p>
                                </div>
                            </div>
                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                <i class="fas fa-landmark text-green-600 text-xl mr-4"></i>
                                <div>
                                    <h4 class="font-semibold text-gray-800">City Museum</h4>
                                    <p class="text-sm text-gray-600">15-minute walk • Cultural experience</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Tab -->
        <div id="faq" class="tab-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- FAQ List -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <h2 class="text-3xl font-bold text-gray-800 mb-8">Frequently Asked Questions</h2>

                        <div class="space-y-6">
                            @foreach($faqs as $index => $faq)
                            <div class="faq-item bg-gray-50 rounded-xl p-6 cursor-pointer" onclick="toggleFAQ({{ $index }})">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $faq['question'] }}</h3>
                                    <i class="fas fa-chevron-down text-primary-red transition-transform duration-300" id="faqIcon{{ $index }}"></i>
                                </div>
                                <div id="faqAnswer{{ $index }}" class="mt-4 text-gray-600 hidden">
                                    <p>{{ $faq['answer'] }}</p>
                                    @if(isset($faq['additional']))
                                    <div class="mt-3 p-3 bg-white rounded-lg">
                                        <p class="text-sm">{{ $faq['additional'] }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Customer Reviews -->
                <div>
                    <div class="bg-white rounded-2xl shadow-xl p-8">
                        <h2 class="text-3xl font-bold text-gray-800 mb-8">Customer Reviews</h2>

                        <div class="space-y-6">
                            @foreach($reviews as $index => $review)
                            <div class="review-card bg-gray-50 rounded-xl p-6" style="animation-delay: {{ $index * 0.1 }}s">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-r from-gray-300 to-gray-400 rounded-full overflow-hidden mr-4">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($review->customer_name) }}&background=random"
                                             alt="{{ $review->customer_name }}">
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-800">{{ $review->customer_name }}</h4>
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <p class="text-gray-600 italic">"{{ Str::limit($review->comment, 120) }}"</p>
                                <p class="text-gray-400 text-sm mt-4">{{ $review->created_at->format('M d, Y') }}</p>
                            </div>
                            @endforeach
                        </div>

                        <!-- Add Review Button -->
                        <div class="mt-8">
                            <button onclick="showReviewModal()"
                                    class="w-full bg-gradient-to-r from-primary-red to-primary-yellow text-white py-4 rounded-lg font-semibold hover:opacity-90 transition duration-300">
                                <i class="fas fa-star mr-2"></i>Add Your Review
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 animate-slide-up">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Add Your Review</h3>
            <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <form id="reviewForm">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-gray-700 mb-2">Your Name</label>
                    <input type="text"
                           name="name"
                           value="{{ auth()->check() ? auth()->user()->name : '' }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-red"
                           required>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Rating</label>
                    <div class="flex space-x-2" id="ratingStars">
                        @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-2xl text-gray-300 cursor-pointer hover:text-yellow-400"
                           data-rating="{{ $i }}"
                           onmouseover="hoverStar({{ $i }})"
                           onmouseout="resetStars()"
                           onclick="selectRating({{ $i }})"></i>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="selectedRating" value="5">
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">Your Review</label>
                    <textarea name="comment"
                              rows="4"
                              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-red"
                              placeholder="Share your experience with us..."
                              required></textarea>
                </div>

                <button type="submit"
                        class="w-full bg-gradient-to-r from-primary-red to-primary-yellow text-white py-4 rounded-lg font-semibold hover:opacity-90 transition duration-300">
                    Submit Review
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Tab functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.contact-tab');
        const tabContents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.dataset.tab;

                // Update active tab
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                // Show selected content
                tabContents.forEach(content => {
                    content.classList.remove('active');
                    content.classList.add('hidden');
                });

                document.getElementById(tabId).classList.remove('hidden');
                document.getElementById(tabId).classList.add('active');
            });
        });
    });

    // FAQ toggle
    function toggleFAQ(index) {
        const answer = document.getElementById('faqAnswer' + index);
        const icon = document.getElementById('faqIcon' + index);

        if(answer.classList.contains('hidden')) {
            answer.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            answer.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }

    // Review modal
    function showReviewModal() {
        @if(auth()->check())
        document.getElementById('reviewModal').classList.remove('hidden');
        document.getElementById('reviewModal').classList.add('flex');
        @else
        Swal.fire({
            title: 'Login Required',
            text: 'Please login to submit a review.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Go to Login'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route("login") }}';
            }
        });
        @endif
    }

    function closeReviewModal() {
        document.getElementById('reviewModal').classList.add('hidden');
        document.getElementById('reviewModal').classList.remove('flex');
    }

    // Star rating
    let currentHoverRating = 0;
    let selectedRating = 5;

    function hoverStar(rating) {
        currentHoverRating = rating;
        updateStars();
    }

    function resetStars() {
        updateStars();
    }

    function selectRating(rating) {
        selectedRating = rating;
        document.getElementById('selectedRating').value = rating;
        updateStars();
    }

    function updateStars() {
        const stars = document.querySelectorAll('#ratingStars i');
        const rating = currentHoverRating || selectedRating;

        stars.forEach((star, index) => {
            if(index < rating) {
                star.classList.add('text-yellow-400');
                star.classList.remove('text-gray-300');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
    }

    // Review form submission
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('{{ route("review.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thank You!',
                    text: 'Your review has been submitted successfully.',
                    timer: 2000,
                    showConfirmButton: false
                });
                closeReviewModal();
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        });
    });

    // Contact form submission
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Send Message?',
            text: 'Are you sure you want to send this message?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, send it!'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });

    // Initialize rating stars
    updateStars();
</script>
@endsection


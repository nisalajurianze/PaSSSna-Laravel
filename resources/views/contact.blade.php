@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<!-- Page Header -->
<section class="bg-gradient-to-r from-red-600 to-yellow-500 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <img src="{{ asset('PASSSNA.png') }}" alt="PaSSSna Logo" class="h-32 w-auto mx-auto mb-6">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Contact Us</h1>
        <p class="text-xl text-white/90">We'd love to hear from you</p>
    </div>
</section>

<!-- Contact Content -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-envelope text-red-500 mr-2"></i>Send us a Message
                </h2>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Your Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                            @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                            @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Subject</label>
                            <select name="type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                                <option value="general">General Inquiry</option>
                                <option value="reservation">Reservation</option>
                                <option value="complaint">Complaint</option>
                                <option value="feedback">Feedback</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Subject</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" required placeholder="What is this about?"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                        @error('subject')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">Your Message</label>
                        <textarea name="message" rows="5" required placeholder="How can we help you?"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all resize-none">{{ old('message') }}</textarea>
                        @error('message')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-red-500 to-yellow-500 text-white font-bold py-4 rounded-lg hover:opacity-90 transition-all transform hover:scale-105">
                        <i class="fas fa-paper-plane mr-2"></i>Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Info -->
            <div class="space-y-8">
                <!-- Location -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="flex items-start">
                        <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-red-500 text-xl"></i>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Our Location</h3>
                            <p class="text-gray-600">
                                123 Gourmet Street<br>
                                Colombo 07, Sri Lanka
                            </p>
                            <a href="#" class="text-red-500 font-medium hover:underline mt-2 inline-block">
                                <i class="fas fa-directions mr-1"></i>Get Directions
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Phone -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="flex items-start">
                        <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-phone-alt text-yellow-500 text-xl"></i>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Phone Number</h3>
                            <p class="text-gray-600">
                                Main: +94 11 234 5678<br>
                                Reservations: +94 11 234 5679
                            </p>
                            <a href="tel:+94112345678" class="text-yellow-600 font-medium hover:underline mt-2 inline-block">
                                <i class="fas fa-phone mr-1"></i>Call Now
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="flex items-start">
                        <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-blue-500 text-xl"></i>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Email Address</h3>
                            <p class="text-gray-600">
                                General: info@passsna.com<br>
                                Reservations: reservations@passsna.com
                            </p>
                            <a href="mailto:info@passsna.com" class="text-blue-500 font-medium hover:underline mt-2 inline-block">
                                <i class="fas fa-envelope mr-1"></i>Send Email
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Opening Hours -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="flex items-start">
                        <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-green-500 text-xl"></i>
                        </div>
                        <div class="ml-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Opening Hours</h3>
                            <div class="space-y-1 text-gray-600">
                                <div class="flex justify-between"><span>Monday - Thursday</span><span>11:00 AM - 10:00 PM</span></div>
                                <div class="flex justify-between"><span>Friday - Saturday</span><span>11:00 AM - 11:00 PM</span></div>
                                <div class="flex justify-between"><span>Sunday</span><span>12:00 PM - 9:00 PM</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Find Us</h2>
        </div>
        <div class="bg-gray-200 rounded-2xl h-96 flex items-center justify-center">
            <div class="text-center text-gray-500">
                <i class="fas fa-map-marked-alt text-6xl mb-4"></i>
                <p class="text-lg">Google Maps Integration</p>
                <p class="text-sm">Add your Google Maps embed code here</p>
            </div>
        </div>
    </div>
</section>
@endsection


@extends('layouts.app')

@section('title', 'FAQ')

@section('content')
<!-- Page Header -->
<section class="bg-gradient-to-r from-red-600 to-yellow-500 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <img src="{{ asset('PASSSNA.png') }}" alt="PaSSSna Logo" class="h-32 w-auto mx-auto mb-6">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Frequently Asked Questions</h1>
        <p class="text-xl text-white/90">Find answers to common questions</p>
    </div>
</section>

<!-- FAQ Content -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4 max-w-4xl">
        <div class="space-y-6">
            <!-- FAQ Items -->
            @php
            $faqs = [
                [
                    'question' => 'What are your operating hours?',
                    'answer' => 'We are open Monday to Thursday from 11:00 AM to 10:00 PM, Friday to Saturday from 11:00 AM to 11:00 PM, and Sunday from 12:00 PM to 9:00 PM.'
                ],
                [
                    'question' => 'Do you offer home delivery?',
                    'answer' => 'Yes! We offer home delivery within a 10km radius. Delivery charge is LKR 300 per order. Estimated delivery time is 45 minutes.'
                ],
                [
                    'question' => 'How can I make a reservation?',
                    'answer' => 'You can make a reservation through our website by clicking on "Reservation" in the navigation menu, or call us at +94 11 234 5678. We recommend booking at least 24 hours in advance.'
                ],
                [
                    'question' => 'What payment methods do you accept?',
                    'answer' => 'We accept cash on delivery, all major credit/debit cards (Visa, Mastercard, American Express), and mobile payments. For dine-in, we also accept digital wallets.'
                ],
                [
                    'question' => 'Do you cater for events and private parties?',
                    'answer' => 'Yes, we offer catering services for events, corporate gatherings, and private parties. Please contact us at reservations@passsna.com or call +94 11 234 5679 for more details and pricing.'
                ],
                [
                    'question' => 'Is there a dress code?',
                    'answer' => 'No, we have a casual dining atmosphere. Come as you are! We want you to feel comfortable while enjoying our food.'
                ],
                [
                    'question' => 'Can I modify or cancel my reservation?',
                    'answer' => 'You can modify or cancel your reservation up to 2 hours before your scheduled time. Please call us or make changes through your account dashboard.'
                ],
                [
                    'question' => 'Do you have vegetarian and vegan options?',
                    'answer' => 'Yes! We have a dedicated vegetarian section on our menu, and several vegan options. Our staff can also customize dishes to accommodate dietary requirements.'
                ],
                [
                    'question' => 'Are your ingredients fresh and locally sourced?',
                    'answer' => 'Absolutely! We pride ourselves on using only the freshest, locally sourced ingredients. Our vegetables are delivered daily from local farms.'
                ],
                [
                    'question' => 'Do you offer gift cards or vouchers?',
                    'answer' => 'Yes, we offer gift cards in denominations of LKR 1,000, LKR 2,500, and LKR 5,000. These can be purchased at the restaurant or ordered online.'
                ],
                [
                    'question' => 'Is there parking available?',
                    'answer' => 'Yes, we have a dedicated parking lot with 20 spaces. Street parking is also available. For larger groups, please let us know in advance.'
                ],
                [
                    'question' => 'Can I bring my own cake for celebrations?',
                    'answer' => 'Yes, you are welcome to bring your own cake. We will provide plates, candles, and matches at no additional charge. Please inform us in advance.'
                ]
            ];
            @endphp

            @foreach($faqs as $index => $faq)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden" x-data="{ open: {{ $index < 2 ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full px-6 py-5 text-left flex items-center justify-between focus:outline-none">
                    <span class="font-semibold text-gray-800 text-lg">{{ $faq['question'] }}</span>
                    <i class="fas transition-transform duration-300" :class="open ? 'fa-chevron-up text-red-500' : 'fa-chevron-down text-gray-400'"></i>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0" class="overflow-hidden">
                    <div class="px-6 pb-6 text-gray-600">
                        {{ $faq['answer'] }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Still Have Questions -->
        <div class="mt-12 bg-gradient-to-r from-red-500 to-yellow-500 rounded-2xl p-8 text-center text-white">
            <h3 class="text-2xl font-bold mb-4">Still Have Questions?</h3>
            <p class="mb-6 text-white/90">Can't find what you're looking for? Contact us directly.</p>
            <a href="{{ route('contact') }}" class="inline-block bg-white text-red-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition-all">
                <i class="fas fa-envelope mr-2"></i>Contact Us
            </a>
        </div>
    </div>
</section>
@endsection


<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index()
    {
        $reviews = Review::with('user')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Frequently Asked Questions
        $faqs = [
            [
                'question' => 'What are your opening hours?',
                'answer' => 'We are open Monday to Thursday from 11:00 AM to 10:00 PM, Friday to Saturday from 11:00 AM to 11:00 PM, and Sunday from 12:00 PM to 9:00 PM.'
            ],
            [
                'question' => 'Do you take reservations?',
                'answer' => 'Yes, we accept reservations through our website, phone, or in person. We recommend booking in advance, especially for weekends and special occasions.'
            ],
            [
                'question' => 'Is there parking available?',
                'answer' => 'Yes, we have complimentary valet parking and a self-parking lot adjacent to the restaurant.'
            ],
            [
                'question' => 'Do you offer vegetarian/vegan options?',
                'answer' => 'Yes, we have a dedicated vegetarian and vegan menu with plenty of options. Please inform your server of any dietary restrictions.'
            ],
            [
                'question' => 'Can I host a private event at your restaurant?',
                'answer' => 'Absolutely! We have private dining rooms available for events. Please contact our events coordinator at events@passsna.com or call us at (555) 123-4567.'
            ],
            [
                'question' => 'Do you offer delivery?',
                'answer' => 'Yes, we offer delivery within a 5-mile radius through our website and mobile app. Delivery charges apply.'
            ],
            [
                'question' => 'What is your cancellation policy for reservations?',
                'answer' => 'We require 2 hours notice for cancellations. For large parties or special events, 24 hours notice is required.'
            ]
        ];

        return view('customer.contact', compact('reviews', 'faqs'));
    }

    public function faq()
    {
        return view('faq');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'category' => 'required|in:general,reservation,feedback,complaint,suggestion',
            'preferred_contact_method' => 'nullable|in:email,phone,both'
        ]);

        // Add user ID if authenticated
        if (Auth::check()) {
            $validated['user_id'] = Auth::id();
        }

        ContactMessage::create($validated);

        return back()->with('success', 'Thank you for your message. We will get back to you soon!');
    }

    public function submitReview(Request $request)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|min:10|max:1000',
            'order_id' => 'nullable|exists:orders,id',
            'anonymous' => 'boolean'
        ]);

        $review = Review::create([
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'order_id' => $validated['order_id'],
            'is_anonymous' => $validated['anonymous'] ?? false,
            'status' => 'pending' // Will be approved by admin
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your review! It will be published after approval.',
            'review' => $review
        ]);
    }

    public function getLocationInfo()
    {
        return response()->json([
            'address' => '123 Gourmet Street, Food City, FC 12345',
            'phone' => '+1 (555) 123-4567',
            'email' => 'info@passsna.com',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'opening_hours' => [
                'Monday - Thursday' => '11:00 AM - 10:00 PM',
                'Friday - Saturday' => '11:00 AM - 11:00 PM',
                'Sunday' => '12:00 PM - 9:00 PM'
            ]
        ]);
    }
}

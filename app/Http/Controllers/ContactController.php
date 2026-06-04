<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Display contact page
     */
    public function index()
    {
        return view('contact');
    }

    /**
     * Store contact message
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'type' => 'nullable|string|in:general,reservation,complaint,feedback'
        ]);

        $message = ContactMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'type' => $validated['type'] ?? 'general',
            'user_id' => Auth::check() ? Auth::id() : null,
            'status' => 'pending'
        ]);

        // Send notification email to admin (optional)
        // Mail::to(config('mail.from.address'))->send(new ContactFormSubmitted($message));

        return redirect()->route('contact')->with('success', 'Your message has been sent successfully! We will get back to you soon.');
    }

    /**
     * Display FAQ page
     */
    public function faq()
    {
        $faqs = [
            [
                'question' => 'What are your operating hours?',
                'answer' => 'We are open Monday to Sunday from 11:00 AM to 11:00 PM.'
            ],
            [
                'question' => 'Do you offer home delivery?',
                'answer' => 'Yes! We deliver within a 10km radius. Delivery charge is Rs. 300.'
            ],
            [
                'question' => 'How can I make a reservation?',
                'answer' => 'You can make a reservation through our website or call us at +94 11 234 5678.'
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept cash on delivery, all major credit/debit cards, and mobile payments.'
            ],
            [
                'question' => 'Do you cater for events?',
                'answer' => 'Yes, we offer catering services for events. Please contact us for more details.'
            ],
            [
                'question' => 'Is there a dress code?',
                'answer' => 'No, we have a casual dining atmosphere. Come as you are!'
            ]
        ];

        return view('faq', compact('faqs'));
    }

    /**
     * Display all messages (Admin)
     */
    public function messages(Request $request)
    {
        $query = ContactMessage::with('user')->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $messages = $query->paginate(20);

        return view('admin.contact.messages', compact('messages'));
    }

    /**
     * Show message details (Admin)
     */
    public function showMessage(ContactMessage $message)
    {
        $message->load('user');

        // Mark as read if not already
        if ($message->status === 'pending') {
            $message->update(['status' => 'read']);
        }

        return view('admin.contact.show', compact('message'));
    }

    /**
     * Reply to message (Admin)
     */
    public function reply(Request $request, ContactMessage $message)
    {
        $validated = $request->validate([
            'reply' => 'required|string|max:2000'
        ]);

        // Update message status
        $message->update([
            'status' => 'replied',
            'reply' => $validated['reply'],
            'replied_at' => now(),
            'replied_by' => Auth::id()
        ]);

        // Send reply email (optional)
        // Mail::to($message->email)->send(new ContactMessageReply($message, $validated['reply']));

        return back()->with('success', 'Reply sent successfully.');
    }

    /**
     * Delete message (Admin)
     */
    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.contact.messages')->with('success', 'Message deleted.');
    }
}

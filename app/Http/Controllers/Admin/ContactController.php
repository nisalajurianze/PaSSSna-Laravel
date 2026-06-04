<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactResponseMail;

class ContactController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->has('status') && $request->status) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            } elseif ($request->status === 'replied') {
                $query->whereNotNull('replied_at');
            }
        }

        if ($request->has('type') && $request->type) {
            $query->where('message_type', $request->type);
        }

        $messages = $query->latest()->paginate(20);

        $stats = [
            'total' => ContactMessage::count(),
            'unread' => 0,
            'read' => 0,
            'replied' => 0,
        ];

        return view('admin.contact.index', compact('messages', 'stats'));
    }

    public function show(ContactMessage $contact)
    {
        if (!$contact->is_read) {
            $contact->update(['is_read' => true]);
        }

        return view('admin.contact.show', compact('contact'));
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        return redirect()->route('admin.contact.index')
            ->with('success', 'Message deleted successfully.');
    }

    public function markAsRead(ContactMessage $message)
    {
        $message->update(['is_read' => true]);
        return back()->with('success', 'Message marked as read.');
    }

    public function markAllAsRead()
    {
        ContactMessage::where('is_read', false)->update(['is_read' => true]);
        return back()->with('success', 'All messages marked as read.');
    }

    public function reply(Request $request, ContactMessage $contact)
    {
        $request->validate([
            'reply_message' => 'required|string',
        ]);

        $contact->update([
            'reply_message' => $request->reply_message,
            'replied_at' => now(),
            'replied_by' => auth()->user()->id,
        ]);

        // Send email reply
        Mail::to($contact->email)->queue(new ContactResponseMail($contact));

        return back()->with('success', 'Reply sent successfully.');
    }
}

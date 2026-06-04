<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    /**
     * Get all messages
     */
    public function index(Request $request)
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

        $messages = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    /**
     * Get single message
     */
    public function show(ContactMessage $message)
    {
        $message->load('user');

        if ($message->status === 'pending') {
            $message->update(['status' => 'read']);
        }

        return response()->json([
            'success' => true,
            'data' => $message
        ]);
    }

    /**
     * Reply to message
     */
    public function reply(Request $request, ContactMessage $message)
    {
        $reply = $request->validate([
            'reply' => 'required|string|max:2000'
        ])['reply'];

        $message->update([
            'status' => 'replied',
            'reply' => $reply,
            'replied_at' => now(),
            'replied_by' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully',
            'data' => $message
        ]);
    }

    /**
     * Delete message
     */
    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully'
        ]);
    }

    /**
     * Get statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => ContactMessage::count(),
            'pending' => ContactMessage::where('status', 'pending')->count(),
            'read' => ContactMessage::where('status', 'read')->count(),
            'replied' => ContactMessage::where('status', 'replied')->count(),
            'by_type' => ContactMessage::groupBy('type')
                ->selectRaw('type, COUNT(*) as count')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}

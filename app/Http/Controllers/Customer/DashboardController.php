<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\User;
use App\Models\MenuItem;
use App\Models\DiningSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $activeDiningSession = DiningSession::where('user_id', $user->id)
            ->where('status', DiningSession::STATUS_ACTIVE)
            ->latest('start_time')
            ->first();

        $activeOrdersCount = Order::where('user_id', $user->id)
            ->whereIn('status', [
                Order::STATUS_PENDING,
                Order::STATUS_CONFIRMED,
                Order::STATUS_PREPARING,
                Order::STATUS_READY,
                Order::STATUS_OUT_FOR_DELIVERY,
                Order::STATUS_SERVED,
            ])
            ->count();

        $recentOrders = Order::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $upcomingReservations = Reservation::where('user_id', $user->id)
            ->whereIn('status', [Reservation::STATUS_PENDING, Reservation::STATUS_CONFIRMED])
            ->whereDate('reservation_date', '>=', now()->toDateString())
            ->orderBy('reservation_date')
            ->orderBy('reservation_time')
            ->limit(5)
            ->get();

        $upcomingReservationsCount = Reservation::where('user_id', $user->id)
            ->whereIn('status', [Reservation::STATUS_PENDING, Reservation::STATUS_CONFIRMED])
            ->whereDate('reservation_date', '>=', now()->toDateString())
            ->count();

        $totalSpent = (float) Order::where('user_id', $user->id)
            ->whereIn('status', [Order::STATUS_COMPLETED, Order::STATUS_DELIVERED, Order::STATUS_SERVED])
            ->selectRaw('COALESCE(SUM(COALESCE(total, total_amount)), 0) as total_spent')
            ->value('total_spent');

        $recommendedItems = MenuItem::available()
            ->where('is_recommended', true)
            ->latest()
            ->limit(6)
            ->get();

        if ($recommendedItems->count() < 3) {
            $fallback = MenuItem::available()
                ->where('is_fast_moving', true)
                ->latest()
                ->limit(6)
                ->get();
            $recommendedItems = $recommendedItems->merge($fallback)->unique('id')->take(6);
        }

        return view('customer.dashboard', compact(
            'activeDiningSession',
            'activeOrdersCount',
            'upcomingReservationsCount',
            'totalSpent',
            'recentOrders',
            'upcomingReservations',
            'recommendedItems'
        ));
    }

    public function orders(Request $request)
    {
        $user = Auth::user();

        $query = Order::where('user_id', $user->id)
            ->with('items.menuItem');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        $query->orderBy('created_at', 'desc');

        $orders = $query->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function showOrder($id)
    {
        $order = Order::with(['items.menuItem', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('customer.orders.show', compact('order'));
    }

    public function cancelOrder($id)
    {
        $order = Order::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $order->update([
            'status' => 'cancelled',
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now()
        ]);

        return back()->with('success', 'Order cancelled successfully.');
    }

    public function reorder($id)
    {
        $oldOrder = Order::with('items')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        // Add items to cart
        $cart = session()->get('cart', []);

        foreach ($oldOrder->items as $item) {
            $cartKey = $item->menu_item_id . '-' . ($item->size ?? 'regular') . '-' . implode(',', $item->toppings ?? []);

            $cart[$cartKey] = [
                'item_id' => $item->menu_item_id,
                'name' => $item->name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'size' => $item->size,
                'toppings' => $item->toppings,
                'total' => $item->price * $item->quantity
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('cart')
            ->with('success', 'Items from previous order added to cart.');
    }

    public function reservations(Request $request)
    {
        $user = Auth::user();

        $query = Reservation::where('user_id', $user->id)
            ->with('tables');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('reservation_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('reservation_date', '<=', $request->date_to);
        }

        // Sort
        $query->orderBy('reservation_date', 'desc');

        $reservations = $query->paginate(10);

        return view('customer.reservations.index', compact('reservations'));
    }

    public function cancelReservation($id)
    {
        $reservation = Reservation::where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'confirmed'])
            ->findOrFail($id);

        // Check cancellation policy (at least 2 hours before)
        $reservationDateTime = Carbon::parse($reservation->reservation_date . ' ' . $reservation->reservation_time);
        if ($reservationDateTime->diffInHours(now()) < 2) {
            return back()->with('error', 'Reservations can only be cancelled at least 2 hours in advance.');
        }

        $reservation->update([
            'status' => 'cancelled',
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now()
        ]);

        // Free tables
        $reservation->tables()->update(['status' => 'available']);

        return back()->with('success', 'Reservation cancelled successfully.');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('customer.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $wantsJson = $request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->isJson();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'phone' => 'required|string|max:20',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
            'promotional_emails' => 'nullable|boolean'
        ]);

        // Update user info
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'zip_code' => $validated['zip'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'email_notifications' => $request->has('email_notifications'),
            'sms_notifications' => $request->has('sms_notifications'),
            'promotional_emails' => $request->has('promotional_emails')
        ]);

        if ($wantsJson) {
            return response()->json(['success' => true, 'message' => 'Profile updated successfully.']);
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $wantsJson = $request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->isJson();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed'
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            if ($wantsJson) {
                return response()->json(['success' => false, 'message' => 'Current password is incorrect.'], 422);
            }
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        if ($wantsJson) {
            return response()->json(['success' => true, 'message' => 'Password updated successfully.']);
        }

        return back()->with('success', 'Password updated successfully.');
    }

    public function deleteAccount(Request $request)
    {
        $wantsJson = $request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->isJson();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Delete user's orders, reservations, etc. in a real app
        // For now, just delete the user
        $user->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($wantsJson) {
            return response()->json(['success' => true, 'message' => 'Account deleted successfully.']);
        }

        return redirect('/')->with('success', 'Your account has been deleted.');
    }

    public function favorites()
    {
        $user = Auth::user();

        // Get favorite items from order history
        $favorites = $this->getFavoriteItems($user->id, 10);

        return view('customer.favorites', compact('favorites'));
    }

    public function notifications()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Mark as read
        $user->notifications()
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return view('customer.notifications', compact('notifications'));
    }

    public function clearNotifications()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->notifications()->delete();

        return back()->with('success', 'Notifications cleared.');
    }

    public function markNotificationAsRead(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'notification_id' => 'required|exists:notifications,id'
        ]);

        $user->notifications()
            ->where('id', $request->notification_id)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        // Return JSON for AJAX requests, redirect for form submissions
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllNotificationsAsRead()
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            \Illuminate\Support\Facades\Log::info('Mark all notifications as read called for user: ' . $user->id);

            $count = $user->notifications()
                ->unread()
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            \Illuminate\Support\Facades\Log::info('Marked ' . $count . ' notifications as read');

            // Return JSON for AJAX requests, redirect for form submissions
            if (request()->expectsJson() || request()->wantsJson()) {
                return response()->json(['success' => true, 'count' => $count]);
            }

            return redirect()->back()->with('success', 'All notifications marked as read.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error marking notifications as read: ' . $e->getMessage());

            if (request()->expectsJson() || request()->wantsJson()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Failed to mark notifications as read.');
        }
    }

    public function checkNewNotifications()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $lastCheck = now()->subSeconds(5);
        $newNotifications = $user->notifications()
            ->where('created_at', '>', $lastCheck)
            ->unread()
            ->count();

        return response()->json([
            'success' => true,
            'new_notifications' => $newNotifications,
            'unread_count' => $user->notifications()->unread()->count()
        ]);
    }

    public function getRecentNotifications()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $notifications = $user->notifications()
            ->latest()
            ->take(5)
            ->get();

        $unreadCount = $user->notifications()->unread()->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function totalSpent()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $orderQuery = Order::where('user_id', $user->id)
            ->whereIn('status', [Order::STATUS_COMPLETED, Order::STATUS_DELIVERED, Order::STATUS_SERVED]);

        $totalSpent = (float) $orderQuery
            ->selectRaw('COALESCE(SUM(COALESCE(total, total_amount)), 0) as total_spent')
            ->value('total_spent');

        $totalOrders = (int) $orderQuery->count();
        $averageOrder = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;

        $orders = Order::where('user_id', $user->id)
            ->whereIn('status', [Order::STATUS_COMPLETED, Order::STATUS_DELIVERED, Order::STATUS_SERVED])
            ->latest()
            ->paginate(10);

        return view('customer.total-spent', compact('totalSpent', 'totalOrders', 'averageOrder', 'orders'));
    }

    private function getFavoriteItems($userId, $limit = 5)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->where('orders.user_id', $userId)
            ->where('orders.status', 'completed')
            ->select(
                'menu_items.id',
                'menu_items.name',
                'menu_items.category',
                'menu_items.image',
                DB::raw('SUM(order_items.quantity) as total_ordered'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
            ->groupBy('menu_items.id', 'menu_items.name', 'menu_items.category', 'menu_items.image')
            ->orderByDesc('total_ordered')
            ->limit($limit)
            ->get();
    }

    private function detectCardType($cardNumber)
    {
        $firstDigit = substr($cardNumber, 0, 1);

        switch ($firstDigit) {
            case '4':
                return 'visa';
            case '5':
                return 'mastercard';
            case '3':
                return 'amex';
            case '6':
                return 'discover';
            default:
                return 'other';
        }
    }
}

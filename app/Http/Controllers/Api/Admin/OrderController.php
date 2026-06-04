<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Get all orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,completed,cancelled'
        ]);

        $order->update(['status' => $validated['status']]);

        if ($validated['status'] === 'completed') {
            $order->update(['completed_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated',
            'data' => $order
        ]);
    }
}

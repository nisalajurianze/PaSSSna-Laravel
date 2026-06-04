<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     */
    public function stats(Request $request)
    {
        $today = now()->startOfDay();

        $stats = [
            'orders' => [
                'today' => Order::whereDate('created_at', today())->count(),
                'pending' => Order::where('status', 'pending')->count(),
                'completed' => Order::where('status', 'completed')->count(),
                'total_revenue' => Order::where('status', 'completed')
                    ->whereDate('completed_at', today())
                    ->sum('total')
            ],
            'reservations' => [
                'today' => Reservation::whereDate('date', today())->count(),
                'pending' => Reservation::where('status', 'pending')->count(),
                'confirmed' => Reservation::where('status', 'confirmed')->count(),
            ],
            'customers' => [
                'total' => User::where('role', 'customer')->count(),
                'active' => User::where('role', 'customer')->where('is_active', true)->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}

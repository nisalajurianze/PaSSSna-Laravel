<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CustomerController extends Controller
{
    /**
     * Get customer profile
     */
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }

    /**
     * Update customer profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|max:20',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    /**
     * Enter dining session
     */
    public function enterDining(Request $request)
    {
        $user = $request->user();

        // Check if already in a dining session
        if ($user->diningSession && $user->diningSession->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Already in a dining session'
            ], 400);
        }

        // Create new dining session
        $diningSession = $user->diningSessions()->create([
            'table_id' => null, // Will be assigned when they select a table
            'started_at' => now(),
            'is_active' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dining session started',
            'data' => $diningSession
        ]);
    }

    /**
     * Exit dining session
     */
    public function exitDining(Request $request)
    {
        $user = $request->user();

        $diningSession = $user->diningSession;

        if (!$diningSession || !$diningSession->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'No active dining session'
            ], 400);
        }

        $diningSession->update([
            'ended_at' => now(),
            'is_active' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dining session ended'
        ]);
    }
}

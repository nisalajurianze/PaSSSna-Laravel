<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyReward;
use App\Models\LoyaltyRedemption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoyaltyController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Get available rewards
        $availableRewards = LoyaltyReward::available()
            ->byPointsAsc()
            ->get();

        // Get user's redemption history
        $redemptions = LoyaltyRedemption::with('reward')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        // Get user's active rewards (not yet used)
        $activeRedemptions = LoyaltyRedemption::with('reward')
            ->where('user_id', $user->id)
            ->where('status', LoyaltyRedemption::STATUS_PENDING)
            ->get();

        return view('customer.loyalty.index', compact(
            'availableRewards',
            'redemptions',
            'activeRedemptions'
        ));
    }

    public function redeem(Request $request)
    {
        $request->validate([
            'reward_id' => 'required|exists:loyalty_rewards,id'
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $reward = LoyaltyReward::findOrFail($request->reward_id);

        // Check if reward is available
        if (!$reward->isAvailable()) {
            return back()->with('error', 'This reward is no longer available.');
        }

        // Check if user has enough points
        $userPoints = $this->getUserPoints($user->id);
        if ($userPoints < $reward->points_required) {
            return back()->with('error', 'You don\'t have enough points to redeem this reward.');
        }

        // Check if user already redeemed this reward recently (prevent duplicate)
        $existingRedemption = LoyaltyRedemption::where('user_id', $user->id)
            ->where('loyalty_reward_id', $reward->id)
            ->where('status', LoyaltyRedemption::STATUS_PENDING)
            ->first();

        if ($existingRedemption) {
            return back()->with('error', 'You have already redeemed this reward.');
        }

        // Generate promo code
        $promoCode = 'LOYALTY-' . strtoupper(Str::random(8));

        // Create redemption record
        DB::transaction(function () use ($user, $reward, $promoCode) {
            LoyaltyRedemption::create([
                'user_id' => $user->id,
                'loyalty_reward_id' => $reward->id,
                'promo_code' => $promoCode,
                'points_used' => $reward->points_required,
                'status' => LoyaltyRedemption::STATUS_PENDING,
            ]);

            // Deduct points from user's orders (simplified - in real app, track user points in user table)
            // For now, we'll track points spent in redemptions
        });

        return redirect()->route('customer.loyalty.index')
            ->with('success', 'Reward redeemed successfully! Promo code: ' . $promoCode);
    }

    public function useReward($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $redemption = LoyaltyRedemption::where('user_id', $user->id)
            ->where('id', $id)
            ->where('status', LoyaltyRedemption::STATUS_PENDING)
            ->firstOrFail();

        // Mark as used
        $redemption->update([
            'status' => LoyaltyRedemption::STATUS_USED,
            'used_at' => now(),
        ]);

        return back()->with('success', 'Reward marked as used!');
    }

    public function getUserPoints($userId)
    {
        // Calculate total points from completed orders
        $totalPoints = \App\Models\Order::where('user_id', $userId)
            ->where('status', 'completed')
            ->count() * 10; // 10 points per completed order

        // Subtract points already used
        $usedPoints = LoyaltyRedemption::where('user_id', $userId)
            ->whereIn('status', [LoyaltyRedemption::STATUS_PENDING, LoyaltyRedemption::STATUS_USED])
            ->sum('points_used');

        return $totalPoints - $usedPoints;
    }
}

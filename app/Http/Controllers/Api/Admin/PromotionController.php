<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PromotionController extends Controller
{
    /**
     * Get all promotions
     */
    public function index(Request $request)
    {
        $query = Promotion::orderBy('start_date', 'desc');

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $promotions = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $promotions
        ]);
    }

    /**
     * Get active promotions for customers
     */
    public function activePromotions()
    {
        $promotions = Promotion::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        return response()->json([
            'success' => true,
            'data' => $promotions
        ]);
    }

    /**
     * Get single promotion
     */
    public function show(Promotion $promotion)
    {
        return response()->json([
            'success' => true,
            'data' => $promotion
        ]);
    }

    /**
     * Create promotion
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:percentage,fixed,buy_one_get_one,special',
            'discount_value' => 'required|numeric|min:0',
            'promo_code' => 'nullable|string|unique:promotions,promo_code',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
            'applicable_items' => 'nullable|array',
            'applicable_categories' => 'nullable|array',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;

        $promotion = Promotion::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Promotion created successfully',
            'data' => $promotion
        ], 201);
    }

    /**
     * Update promotion
     */
    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'sometimes|in:percentage,fixed,buy_one_get_one,special',
            'discount_value' => 'sometimes|numeric|min:0',
            'promo_code' => 'sometimes|string|unique:promotions,promo_code,' . $promotion->id,
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $promotion->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Promotion updated successfully',
            'data' => $promotion
        ]);
    }

    /**
     * Delete promotion
     */
    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Promotion deleted successfully'
        ]);
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(Promotion $promotion)
    {
        $promotion->update(['is_active' => !$promotion->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated',
            'data' => $promotion
        ]);
    }

    /**
     * Validate promo code
     */
    public function validatePromo(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string',
            'order_amount' => 'required|numeric|min:0',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $promo = Promotion::where('promo_code', $request->promo_code)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired promo code'
            ], 400);
        }

        // Check minimum order amount
        if ($promo->min_order_amount && $request->order_amount < $promo->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum order amount not met'
            ], 400);
        }

        // Check usage limit
        if ($promo->usage_limit && $promo->used_count >= $promo->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code usage limit reached'
            ], 400);
        }

        // Calculate discount
        $discount = 0;
        if ($promo->type === 'percentage') {
            $discount = ($request->order_amount * $promo->discount_value) / 100;
            if ($promo->max_discount && $discount > $promo->max_discount) {
                $discount = $promo->max_discount;
            }
        } else {
            $discount = $promo->discount_value;
        }

        return response()->json([
            'success' => true,
            'message' => 'Promo code valid',
            'data' => [
                'promotion' => $promo,
                'discount' => $discount
            ]
        ]);
    }

    /**
     * Get promotion statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => Promotion::count(),
            'active' => Promotion::where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->count(),
            'expired' => Promotion::where('end_date', '<', now())->count(),
            'upcoming' => Promotion::where('start_date', '>', now())->count(),
            'by_type' => Promotion::groupBy('type')
                ->selectRaw('type, COUNT(*) as count')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}

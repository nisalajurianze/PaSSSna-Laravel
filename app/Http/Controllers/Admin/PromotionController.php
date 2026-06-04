<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\MenuItem;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = Promotion::query();

        if ($request->has('status') && $request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true)
                      ->where('start_date', '<=', now())
                      ->where('end_date', '>=', now());
            } elseif ($request->status === 'upcoming') {
                $query->where('is_active', true)->where('start_date', '>', now());
            } elseif ($request->status === 'expired') {
                $query->where(function($q) {
                    $q->where('is_active', false)->orWhere('end_date', '<', now());
                });
            }
        }

        if ($request->has('type') && $request->type) {
            $query->where('promotion_type', $request->type);
        }

        $promotions = $query->latest()->paginate(20);

        $stats = [
            'total' => Promotion::count(),
            'active' => Promotion::where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->count(),
            'upcoming' => Promotion::where('is_active', true)
                ->where('start_date', '>', now())
                ->count(),
            'expired' => Promotion::where('is_active', false)
                ->orWhere('end_date', '<', now())
                ->count(),
        ];

        return view('admin.promotions.index', compact('promotions', 'stats'));
    }

    public function create()
    {
        $menuItems = MenuItem::where('is_available', true)->get();
        $categories = Category::all();
        return view('admin.promotions.create', compact('menuItems', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'promotion_type' => 'required|in:percentage,fixed,buy_x_get_y,bogo',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        Promotion::create($data);

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promotion created successfully.');
    }

    public function show(Promotion $promotion)
    {
        $promotion->load(['usage' => function($query) {
            $query->with('order')->latest()->limit(20);
        }]);

        return view('admin.promotions.show', compact('promotion'));
    }

    public function edit(Promotion $promotion)
    {
        $menuItems = MenuItem::where('is_available', true)->get();
        $categories = Category::where('is_active', true)->get();
        return view('admin.promotions.edit', compact('promotion', 'menuItems', 'categories'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'promotion_type' => 'required|in:percentage,fixed,buy_x_get_y,bogo',
            'discount_value' => 'required|numeric|min:0',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $promotion->update($data);

        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promotion updated successfully.');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return redirect()->route('admin.promotions.index')
            ->with('success', 'Promotion deleted successfully.');
    }

    public function toggle(Promotion $promotion)
    {
        $promotion->update(['is_active' => !$promotion->is_active]);
        return back()->with('success', 'Promotion status toggled.');
    }

    public function analytics(Promotion $promotion)
    {
        $stats = [
            'total_usage' => $promotion->usage()->count(),
            'total_discount_given' => $promotion->usage()->sum('discount_amount'),
            'total_revenue_generated' => $promotion->usage()->sum('order_total'),
            'used_count' => $promotion->usage()->where('used', true)->count(),
        ];

        $recentUsage = $promotion->usage()->with('order')->latest()->limit(10)->get();

        return view('admin.promotions.analytics', compact('promotion', 'stats', 'recentUsage'));
    }
}

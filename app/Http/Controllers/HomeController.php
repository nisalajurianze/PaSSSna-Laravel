<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Promotion;
use App\Models\Review;
use App\Models\Table;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Featured items (fast moving)
        $fastMovingItems = MenuItem::where('is_fast_moving', true)
            ->where('is_available', true)
            ->limit(8)
            ->get();

        // Active promotions
        $promotions = Promotion::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->limit(3)
            ->get();

        // Customer reviews
        $reviews = Review::where('is_approved', true)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Available tables count
        $availableTables = Table::where('status', 'available')->count();

        return view('welcome', compact(
            'fastMovingItems',
            'promotions',
            'reviews',
            'availableTables'
        ));
    }

    public function about()
    {
        return view('about');
    }

    public function privacyPolicy()
    {
        return view('privacy-policy');
    }

    public function termsConditions()
    {
        return view('terms-conditions');
    }
}

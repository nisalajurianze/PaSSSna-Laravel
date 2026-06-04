<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = MenuItem::where('is_available', true);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ingredients', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhereRaw("REPLACE(category, '_', ' ') like ?", ["%{$search}%"]);
            });
        }

        // Filter by category
        if ($request->filled('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        // Dietary preferences
        if ($request->filled('vegetarian')) {
            $query->where('food_type', 'vegetarian');
        }

        if ($request->filled('vegan')) {
            $query->where('food_type', 'vegan');
        }

        // Sort options
        $sort = $request->get('sort', 'name');
        $order = $request->get('order', 'asc');

        switch ($sort) {
            case 'price_low':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('base_price', 'desc');
                break;
            case 'popular':
                $query->orderBy('total_orders', 'desc');
                break;
            default:
                $query->orderBy('name', $order);
        }

        $menuItems = $query->paginate(12);

        $categories = MenuItem::where('is_available', true)
            ->distinct()
            ->pluck('category');

        $fastMoving = MenuItem::where('is_fast_moving', true)
            ->where('is_available', true)
            ->limit(6)
            ->get();

        $offers = MenuItem::whereNotNull('offer_price')
            ->where('is_available', true)
            ->whereDate('offer_valid_until', '>=', now())
            ->limit(6)
            ->get();

        return view('customer.menu.index', compact(
            'menuItems',
            'categories',
            'fastMoving',
            'offers'
        ));
    }

    public function show($id)
    {
        $menu = MenuItem::where('is_available', true)->findOrFail($id);

        $menu->flavors = $menu->flavors ?? [];
        $menu->sizes = $menu->sizes ?? [];
        $menu->extra_toppings = $menu->extra_toppings ?? [];
        $menu->ingredients = $menu->ingredients ?? [];

        $relatedItems = MenuItem::where('category', $menu->category)
            ->where('id', '!=', $id)
            ->where('is_available', true)
            ->limit(4)
            ->get();

        $frequentlyBoughtTogether = $this->getFrequentlyBoughtTogether($id);

        $reviews = \App\Models\Review::whereHas('order.items', function ($query) use ($id) {
                $query->where('menu_item_id', $id);
            })
            ->where('status', 'approved')
            ->with('user')
            ->limit(5)
            ->get();

        return view('customer.menu.show', compact('menu', 'relatedItems', 'reviews', 'frequentlyBoughtTogether'));
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1|max:10',
            'size' => 'nullable|string|max:50',
            'flavor' => 'nullable|string',
            'toppings' => 'nullable|array',
            'special_instructions' => 'nullable|string|max:500'
        ]);

        $item = MenuItem::find($validated['item_id']);

        if (!$item->is_available) {
            return response()->json([
                'success' => false,
                'message' => 'This item is currently unavailable.'
            ]);
        }

        $price = $this->calculateItemPrice($item, $validated['size'] ?? 'regular');

        if (!empty($validated['flavor']) && is_array($item->flavors)) {
            $flavorName = $validated['flavor'];
            if (isset($item->flavors[$flavorName]) && is_numeric($item->flavors[$flavorName])) {
                $price += (float) $item->flavors[$flavorName];
            } else {
                foreach ($item->flavors as $flavor) {
                    if (is_array($flavor) && ($flavor['name'] ?? null) === $flavorName) {
                        $price += (float) ($flavor['price'] ?? 0);
                        break;
                    }
                }
            }
        }

        $toppings = $validated['toppings'] ?? [];
        foreach ($toppings as $topping) {
            if (isset($item->extra_toppings[$topping])) {
                $price += $item->extra_toppings[$topping];
            } elseif (is_array($item->extra_toppings)) {
                foreach ($item->extra_toppings as $extra) {
                    if (is_array($extra) && ($extra['name'] ?? null) === $topping) {
                        $price += (float) ($extra['price'] ?? 0);
                        break;
                    }
                }
            }
        }

        $cart = session()->get('cart', []);
        $cartKey = $this->generateCartKey($item->id, $validated);

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $validated['quantity'];
            $cart[$cartKey]['total'] = $cart[$cartKey]['quantity'] * $cart[$cartKey]['price'];
        } else {
            $cart[$cartKey] = [
                'item_id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'quantity' => $validated['quantity'],
                'price' => $price,
                'size' => $validated['size'] ?? 'regular',
                'flavor' => $validated['flavor'] ?? null,
                'toppings' => $toppings,
                'special_instructions' => $validated['special_instructions'] ?? null,
                'image' => $item->image,
                'total' => $price * $validated['quantity']
            ];
        }

        session()->put('cart', $cart);

        $cartCount = $this->getCartCount($cart);
        $cartTotal = $this->getCartTotal($cart);

        return response()->json([
            'success' => true,
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal,
            'message' => 'Item added to cart successfully.',
            'item' => [
                'name' => $item->name,
                'quantity' => $validated['quantity'],
                'price' => $price
            ]
        ]);
    }

    public function quickView($id)
    {
        $item = MenuItem::where('is_available', true)->findOrFail($id);

        $item->flavors = $item->flavors ?? [];
        $item->sizes = $item->sizes ?? [];
        $item->extra_toppings = $item->extra_toppings ?? [];

        return response()->json([
            'success' => true,
            'item' => $item
        ]);
    }

    public function getCategories()
    {
        $categories = MenuItem::where('is_available', true)
            ->distinct()
            ->pluck('category');

        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    public function byCategory(Request $request, $category)
    {
        $query = MenuItem::where('is_available', true)
            ->where('category', $category);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $menuItems = $query->paginate(12);

        $categories = MenuItem::where('is_available', true)
            ->distinct()
            ->pluck('category');

        $fastMoving = MenuItem::where('is_fast_moving', true)
            ->where('is_available', true)
            ->limit(6)
            ->get();

        $offers = MenuItem::whereNotNull('offer_price')
            ->where('is_available', true)
            ->whereDate('offer_valid_until', '>=', now())
            ->limit(6)
            ->get();

        return view('customer.menu.index', compact(
            'menuItems',
            'categories',
            'fastMoving',
            'offers'
        ));
    }

    public function search(Request $request)
    {
        $search = $request->get('q', '');

        if (strlen($search) < 2) {
            return response()->json([
                'success' => true,
                'items' => [],
                'count' => 0
            ]);
        }

        $items = MenuItem::where('is_available', true)
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhereRaw("REPLACE(category, '_', ' ') like ?", ["%{$search}%"]);
            })
            ->limit(10)
            ->get(['id', 'name', 'category', 'base_price', 'image', 'offer_price'])
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'category' => $item->category,
                    'price' => (float) $item->price,
                    'image' => $item->image,
                    'image_url' => $item->image_url,
                    'offer_price' => $item->offer_price,
                ];
            });

        return response()->json([
            'success' => true,
            'items' => $items,
            'count' => $items->count()
        ]);
    }

    public function filter(Request $request)
    {
        $query = MenuItem::where('is_available', true);

        if ($request->filled('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        if ($request->filled('vegetarian')) {
            $query->where('food_type', 'vegetarian');
        }

        if ($request->filled('vegan')) {
            $query->where('food_type', 'vegan');
        }

        $items = $query->get();

        return response()->json([
            'success' => true,
            'items' => $items,
            'count' => $items->count()
        ]);
    }

    private function calculateItemPrice($item, $size)
    {
        $price = $item->price;

        if ($size && is_array($item->sizes)) {
            if (isset($item->sizes[$size]) && is_numeric($item->sizes[$size])) {
                $price = $item->sizes[$size];
            } else {
                foreach ($item->sizes as $sizeEntry) {
                    if (is_array($sizeEntry) && strcasecmp($sizeEntry['name'] ?? '', $size) === 0) {
                        if (isset($sizeEntry['price'])) {
                            $price = (float) $sizeEntry['price'];
                        } else {
                            $price = $item->price + (float) ($sizeEntry['price_modifier'] ?? 0);
                        }
                        break;
                    }
                }
            }
        }

        if ($item->offer_price && $item->offer_valid_until >= now()) {
            $price = $item->offer_price;
        }

        return $price;
    }

    private function getFrequentlyBoughtTogether($itemId, $limit = 4)
    {
        $frequentlyBoughtIds = DB::table('order_items as oi1')
            ->join('order_items as oi2', 'oi1.order_id', '=', 'oi2.order_id')
            ->where('oi1.menu_item_id', $itemId)
            ->where('oi2.menu_item_id', '!=', $itemId)
            ->select('oi2.menu_item_id', DB::raw('COUNT(oi2.menu_item_id) as frequency'))
            ->groupBy('oi2.menu_item_id')
            ->orderByDesc('frequency')
            ->limit($limit)
            ->pluck('menu_item_id');

        if ($frequentlyBoughtIds->isEmpty()) {
            return collect();
        }

        $orderedIds = $frequentlyBoughtIds->values()->all();

        if (empty($orderedIds)) {
            return collect();
        }

        $caseSql = 'CASE id ';
        foreach ($orderedIds as $index => $id) {
            $caseSql .= 'WHEN ' . (int) $id . ' THEN ' . $index . ' ';
        }
        $caseSql .= 'ELSE ' . count($orderedIds) . ' END';

        return MenuItem::whereIn('id', $orderedIds)
            ->where('is_available', true)
            ->orderByRaw($caseSql)
            ->get();
    }

    private function generateCartKey($itemId, $options)
    {
        $key = $itemId;
        $key .= '_' . ($options['size'] ?? 'regular');
        $key .= '_' . ($options['flavor'] ?? 'default');

        if (!empty($options['toppings'])) {
            sort($options['toppings']);
            $key .= '_' . implode(',', $options['toppings']);
        }

        return md5($key);
    }

    private function getCartCount($cart)
    {
        return count($cart);
    }

    private function getCartTotal($cart)
    {
        return array_sum(array_column($cart, 'total'));
    }

    public function getCartSummary()
    {
        $cart = session()->get('cart', []);

        return response()->json([
            'count' => count($cart),
            'total' => $this->getCartTotal($cart),
            'items' => array_values($cart)
        ]);
    }

    public function checkAvailability($id)
    {
        $item = MenuItem::find($id);

        if (!$item) {
            return response()->json([
                'available' => false,
                'message' => 'Item not found.'
            ]);
        }

        return response()->json([
            'available' => $item->is_available,
            'preparation_time' => $item->preparation_time,
            'message' => $item->is_available ? 'Available' : 'Currently unavailable'
        ]);
    }

    public function checkUpdated(Request $request)
    {
        $lastCheck = $request->get('last_check', now()->subMinutes(5)->toIso8601String());

        $hasUpdates = MenuItem::where('updated_at', '>', $lastCheck)
            ->orWhere('created_at', '>', $lastCheck)
            ->exists();

        return response()->json([
            'updated' => $hasUpdates,
            'timestamp' => now()->toIso8601String()
        ]);
    }
}

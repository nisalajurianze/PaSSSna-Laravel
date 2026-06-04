<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Get all menu items with filters
     */
    public function index(Request $request)
    {
        $query = MenuItem::with('category', 'ingredients');

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by availability
        if ($request->filled('is_available')) {
            $query->where('is_available', $request->boolean('is_available'));
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $menuItems = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $menuItems
        ]);
    }

    /**
     * Get single menu item
     */
    public function show(MenuItem $menuItem)
    {
        $menuItem->load('category', 'ingredients');

        return response()->json([
            'success' => true,
            'data' => $menuItem
        ]);
    }

    /**
     * Create new menu item
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'nullable|boolean',
            'is_fast_moving' => 'nullable|boolean',
            'offer_price' => 'nullable|numeric|min:0',
            'preparation_time' => 'nullable|integer|min:0',
            'calories' => 'nullable|integer|min:0',
            'ingredients' => 'nullable|array',
            'ingredients.*.id' => 'exists:ingredients,id',
            'ingredients.*.quantity' => 'nullable|numeric|min:0',
            'flavors' => 'nullable|array',
            'sizes' => 'nullable|array',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu-images', 'public');
            $validated['image'] = $imagePath;
        }

        $menuItem = MenuItem::create($validated);

        // Sync ingredients
        if ($request->has('ingredients')) {
            $pivotData = [];
            foreach ($request->ingredients as $ingredient) {
                $pivotData[$ingredient['id']] = ['quantity' => $ingredient['quantity'] ?? 1];
            }
            $menuItem->ingredients()->sync($pivotData);
        }

        $menuItem->load('category', 'ingredients');

        return response()->json([
            'success' => true,
            'message' => 'Menu item created successfully',
            'data' => $menuItem
        ], 201);
    }

    /**
     * Update menu item
     */
    public function update(Request $request, MenuItem $menuItem)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'sometimes|numeric|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'nullable|boolean',
            'is_fast_moving' => 'nullable|boolean',
            'offer_price' => 'nullable|numeric|min:0',
            'preparation_time' => 'nullable|integer|min:0',
            'calories' => 'nullable|integer|min:0',
            'ingredients' => 'nullable|array',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $imagePath = $request->file('image')->store('menu-images', 'public');
            $validated['image'] = $imagePath;
        }

        $menuItem->update($validated);

        // Sync ingredients
        if ($request->has('ingredients')) {
            $pivotData = [];
            foreach ($request->ingredients as $ingredient) {
                $pivotData[$ingredient['id']] = ['quantity' => $ingredient['quantity'] ?? 1];
            }
            $menuItem->ingredients()->sync($pivotData);
        }

        $menuItem->load('category', 'ingredients');

        return response()->json([
            'success' => true,
            'message' => 'Menu item updated successfully',
            'data' => $menuItem
        ]);
    }

    /**
     * Delete menu item
     */
    public function destroy(MenuItem $menuItem)
    {
        // Delete image
        if ($menuItem->image) {
            Storage::disk('public')->delete($menuItem->image);
        }

        $menuItem->ingredients()->detach();
        $menuItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu item deleted successfully'
        ]);
    }

    /**
     * Toggle availability
     */
    public function toggleAvailability(MenuItem $menuItem)
    {
        $menuItem->update(['is_available' => !$menuItem->is_available]);

        return response()->json([
            'success' => true,
            'message' => 'Availability updated',
            'data' => $menuItem
        ]);
    }

    /**
     * Get categories
     */
    public function getCategories()
    {
        $categories = Category::withCount('menuItems')->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get ingredients
     */
    public function getIngredients()
    {
        $ingredients = Ingredient::all();

        return response()->json([
            'success' => true,
            'data' => $ingredients
        ]);
    }

    /**
     * Get menu statistics
     */
    public function statistics()
    {
        $stats = [
            'total_items' => MenuItem::count(),
            'available_items' => MenuItem::where('is_available', true)->count(),
            'unavailable_items' => MenuItem::where('is_available', false)->count(),
            'items_with_offer' => MenuItem::whereNotNull('offer_price')->count(),
            'fast_moving_items' => MenuItem::where('is_fast_moving', true)->count(),
            'by_category' => Category::withCount('menuItems')->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}

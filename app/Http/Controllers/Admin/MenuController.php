<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Category;
use App\Models\CustomIngredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = MenuItem::query();

        if ($request->has('category') && $request->category) {
            $categoryFilter = $this->normalizeCategoryValue($request->category);
            if ($categoryFilter) {
                $query->where('category', $categoryFilter);
            }
        }

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status')) {
            if ($request->status === 'available') {
                $query->where('is_available', true);
            } elseif ($request->status === 'unavailable') {
                $query->where('is_available', false);
            }
        }

        $menuItems = $query->latest()->paginate(20);
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $categories = MenuItem::query()
                ->select('category')
                ->whereNotNull('category')
                ->where('category', '!=', '')
                ->distinct()
                ->orderBy('category')
                ->pluck('category')
                ->filter()
                ->map(function ($category) {
                    return (object) [
                        'name' => Str::title(str_replace(['_', '-'], ' ', $category)),
                        'slug' => $category,
                    ];
                });
        }

        return view('admin.menu.index', compact('menuItems', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $ingredients = CustomIngredient::all();
        return view('admin.menu.create', compact('categories', 'ingredients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'category' => ['required', 'string', Rule::in($this->allowedCategories())],
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'preparation_time' => 'nullable|integer|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'offer_valid_from' => 'nullable|date',
            'offer_valid_to' => 'nullable|date',
            'offer_valid_until' => 'nullable|date',
            'food_type' => ['nullable', Rule::in(['vegetarian', 'non_vegetarian', 'vegan'])],
            'min_order_qty' => 'nullable|integer|min:1',
            'max_order_qty' => 'nullable|integer|min:1',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sizes' => 'nullable|array',
            'flavors' => 'nullable|array',
            'extra_toppings' => 'nullable|array',
        ]);

        $categoryValue = $this->resolveCategoryValue($request);
        $basePrice = (float) $request->price;
        $slugSource = $request->filled('slug') ? $request->slug : $request->name;

        $sizesInput = $request->input('sizes', []);
        [$sizes, $sizeGradients] = $this->parseSizes($sizesInput, $basePrice);
        $flavors = $this->parseFlavors($request);
        $toppings = $this->parseToppings($request->input('extra_toppings', []));
        $variantImages = $this->parseVariantImages($request);
        $nutritionInfo = $this->parseNutritionInfo($request->input('nutrition_info'));
        $nutritionInfo['size_gradients'] = $sizeGradients;
        $nutritionInfo['variant_images'] = $variantImages;

        $ingredients = [];
        if ($request->filled('ingredients_text')) {
            $ingredients = array_values(array_filter(array_map('trim', explode(',', $request->ingredients_text))));
        }

        $data = [
            'name' => $request->name,
            'slug' => $this->buildUniqueSlug($slugSource),
            'category' => $categoryValue ?? 'custom',
            'base_price' => $basePrice,
            'description' => $request->description,
            'short_description' => $request->short_description ?: Str::limit($request->description, 140),
            'preparation_time' => $request->preparation_time,
            'food_type' => $request->food_type ?: 'non_vegetarian',
            'is_available' => $request->has('is_available') ? (bool) $request->is_available : true,
            'is_fast_moving' => (bool) $request->is_fast_moving,
            'is_recommended' => (bool) $request->is_recommended,
            'is_customizable' => (bool) $request->is_customizable,
            'offer_price' => $request->offer_price,
            'offer_valid_from' => $request->offer_valid_from,
            'offer_valid_to' => $request->offer_valid_to,
            'offer_valid_until' => $request->offer_valid_until,
            'min_order_qty' => $request->min_order_qty ?? 1,
            'max_order_qty' => $request->max_order_qty ?? 10,
            'sort_order' => $request->sort_order ?? 0,
            'sizes' => $sizes,
            'flavors' => $flavors,
            'extra_toppings' => $toppings,
            'ingredients' => $ingredients,
            'nutrition_info' => $nutritionInfo,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu', 'public');
        }

        MenuItem::create($data);

        return redirect()->route('admin.menu.index')
            ->with('success', 'Menu item created successfully.');
    }

    public function show(MenuItem $menu)
    {
        return view('admin.menu.show', ['item' => $menu]);
    }

    public function edit(MenuItem $menu)
    {
        $categories = Category::all();
        $ingredients = CustomIngredient::all();
        return view('admin.menu.edit', ['item' => $menu, 'categories' => $categories, 'ingredients' => $ingredients]);
    }

    public function update(Request $request, MenuItem $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'category' => ['required', 'string', Rule::in($this->allowedCategories())],
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'short_description' => 'nullable|string|max:255',
            'preparation_time' => 'nullable|integer|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'offer_valid_from' => 'nullable|date',
            'offer_valid_to' => 'nullable|date',
            'offer_valid_until' => 'nullable|date',
            'food_type' => ['nullable', Rule::in(['vegetarian', 'non_vegetarian', 'vegan'])],
            'min_order_qty' => 'nullable|integer|min:1',
            'max_order_qty' => 'nullable|integer|min:1',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sizes' => 'nullable|array',
            'flavors' => 'nullable|array',
            'extra_toppings' => 'nullable|array',
        ]);

        $categoryValue = $this->resolveCategoryValue($request);
        $basePrice = (float) $request->price;
        $slugSource = $request->filled('slug') ? $request->slug : ($menu->slug ?: $request->name);

        $sizesInput = $request->input('sizes', []);
        [$sizes, $sizeGradients] = $this->parseSizes($sizesInput, $basePrice);
        $flavors = $this->parseFlavors($request);
        $toppings = $this->parseToppings($request->input('extra_toppings', []));
        $variantImages = $this->parseVariantImages($request);
        $nutritionInfo = $this->parseNutritionInfo(
            $request->input('nutrition_info'),
            is_array($menu->nutrition_info) ? $menu->nutrition_info : []
        );
        $nutritionInfo['size_gradients'] = $sizeGradients;
        $nutritionInfo['variant_images'] = $variantImages;

        $ingredients = [];
        if ($request->filled('ingredients_text')) {
            $ingredients = array_values(array_filter(array_map('trim', explode(',', $request->ingredients_text))));
        }

        $menu->update([
            'name' => $request->name,
            'slug' => $this->buildUniqueSlug($slugSource, $menu->id),
            'category' => $categoryValue ?? 'custom',
            'base_price' => $basePrice,
            'description' => $request->description,
            'short_description' => $request->short_description ?: Str::limit($request->description, 140),
            'preparation_time' => $request->preparation_time,
            'food_type' => $request->food_type ?: 'non_vegetarian',
            'is_available' => (bool) $request->is_available,
            'is_fast_moving' => (bool) $request->is_fast_moving,
            'is_recommended' => (bool) $request->is_recommended,
            'is_customizable' => (bool) $request->is_customizable,
            'offer_price' => $request->offer_price,
            'offer_valid_from' => $request->offer_valid_from,
            'offer_valid_to' => $request->offer_valid_to,
            'offer_valid_until' => $request->offer_valid_until,
            'min_order_qty' => $request->min_order_qty ?? 1,
            'max_order_qty' => $request->max_order_qty ?? 10,
            'sort_order' => $request->sort_order ?? 0,
            'sizes' => $sizes,
            'flavors' => $flavors,
            'extra_toppings' => $toppings,
            'ingredients' => $ingredients,
            'nutrition_info' => $nutritionInfo,
        ]);

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $menu->update(['image' => $request->file('image')->store('menu', 'public')]);
        }

        return redirect()->route('admin.menu.show', $menu->id)
            ->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $menu)
    {
        $menu->delete();

        return back()->with('success', 'Menu item deleted successfully.');
    }

    public function toggleAvailability(MenuItem $item)
    {
        $item->update(['is_available' => !$item->is_available]);

        $status = $item->is_available ? 'available' : 'unavailable';
        return back()->with('success', "Item is now {$status}.");
    }

    public function export()
    {
        $menuItems = MenuItem::all();

        $csv = "Name,Category,Price,Available,Sizes,Variants,Addons\n";

        foreach ($menuItems as $item) {
            $sizes = is_array($item->sizes) ? implode(', ', array_keys($item->sizes)) : '';
            $variants = is_array($item->flavors) ? implode(', ', array_map(function ($flavor) {
                return is_array($flavor) ? ($flavor['name'] ?? '') : (string) $flavor;
            }, $item->flavors)) : '';
            $addons = is_array($item->extra_toppings) ? implode(', ', array_keys($item->extra_toppings)) : '';

            $csv .= "\"{$item->name}\",\"{$item->category}\",{$item->price},{$item->is_available},\"{$sizes}\",\"{$variants}\",\"{$addons}\"\n";
        }

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename=\"menu-export.csv\"');
    }

    public function ingredients()
    {
        $ingredients = CustomIngredient::latest()->paginate(20);
        return view('admin.ingredients.index', compact('ingredients'));
    }

    public function storeIngredient(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string',
            'current_quantity' => 'required|numeric|min:0',
            'minimum_quantity' => 'required|numeric|min:0',
        ]);

        CustomIngredient::create($request->all());

        return back()->with('success', 'Ingredient added successfully.');
    }

    public function updateIngredient(Request $request, CustomIngredient $ingredient)
    {
        $ingredient->update($request->all());
        return back()->with('success', 'Ingredient updated successfully.');
    }

    public function destroyIngredient(CustomIngredient $ingredient)
    {
        $ingredient->delete();
        return back()->with('success', 'Ingredient deleted successfully.');
    }

    private function resolveCategoryValue(Request $request): ?string
    {
        if ($request->filled('category')) {
            return $this->normalizeCategoryValue($request->category);
        }

        if ($request->filled('category_id')) {
            $category = Category::find($request->category_id);
            if ($category) {
                return $this->normalizeCategoryValue($category->slug ?? $category->name);
            }
        }

        return null;
    }

    private function allowedCategories(): array
    {
        return [
            'appetizer',
            'main_course',
            'dessert',
            'beverage',
            'special',
            'custom',
        ];
    }

    private function normalizeCategoryValue(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $normalized = Str::of($value)
            ->lower()
            ->replace(['-', ' '], '_')
            ->__toString();

        $aliases = [
            'main' => 'main_course',
            'maincourse' => 'main_course',
            'drinks' => 'beverage',
            'drink' => 'beverage',
        ];

        if (isset($aliases[$normalized])) {
            $normalized = $aliases[$normalized];
        }

        return in_array($normalized, $this->allowedCategories(), true) ? $normalized : null;
    }

    private function buildUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (MenuItem::where('slug', $slug)
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function parseSizes(array $sizesInput, float $basePrice): array
    {
        $sizes = [];
        $gradients = [];

        foreach ($sizesInput as $size) {
            if (is_string($size)) {
                $name = trim($size);
                if ($name === '') {
                    continue;
                }
                $sizes[$name] = $basePrice;
                continue;
            }

            if (!is_array($size)) {
                continue;
            }

            $name = trim($size['name'] ?? '');
            if ($name === '') {
                continue;
            }

            $price = $size['price'] ?? null;
            if ($price === null && isset($size['price_modifier'])) {
                $price = $basePrice + (float) $size['price_modifier'];
            }

            $sizes[$name] = (float) ($price ?? $basePrice);

            if (!empty($size['gradient'])) {
                $gradients[$name] = $size['gradient'];
            }
        }

        return [$sizes, $gradients];
    }

    private function parseFlavors(Request $request): array
    {
        $flavors = [];
        $inputs = $request->input('flavors', []);
        $files = $request->file('flavors', []);

        foreach ($inputs as $index => $input) {
            if (is_string($input)) {
                $name = trim($input);
                if ($name === '') {
                    continue;
                }
                $flavors[] = [
                    'name' => $name,
                    'price' => 0,
                    'color' => '#DC2626',
                    'image' => null,
                ];
                continue;
            }

            if (!is_array($input)) {
                continue;
            }

            $name = trim($input['name'] ?? '');
            if ($name === '') {
                continue;
            }

            $image = $input['existing_image'] ?? null;
            if (isset($files[$index]['image']) && $files[$index]['image']->isValid()) {
                $image = $files[$index]['image']->store('menu/flavors', 'public');
            }

            $flavors[] = [
                'name' => $name,
                'price' => (float) ($input['price'] ?? 0),
                'color' => $input['color'] ?? '#DC2626',
                'image' => $image,
            ];
        }

        return $flavors;
    }

    private function parseToppings(array $toppingsInput): array
    {
        $toppings = [];
        foreach ($toppingsInput as $topping) {
            if (is_string($topping)) {
                $name = trim($topping);
                if ($name === '') {
                    continue;
                }
                $toppings[$name] = 0;
                continue;
            }

            if (!is_array($topping)) {
                continue;
            }

            $name = trim($topping['name'] ?? '');
            if ($name === '') {
                continue;
            }

            $toppings[$name] = (float) ($topping['price'] ?? 0);
        }

        return $toppings;
    }

    private function parseVariantImages(Request $request): array
    {
        $variants = [];
        $inputs = $request->input('variant_images', []);
        $files = $request->file('variant_images', []);

        foreach ($inputs as $index => $input) {
            if (!is_array($input)) {
                continue;
            }

            $flavor = trim($input['flavor'] ?? '');
            $size = trim($input['size'] ?? '');
            if ($flavor === '' || $size === '') {
                continue;
            }

            $image = $input['existing_image'] ?? null;
            if (isset($files[$index]['image']) && $files[$index]['image']->isValid()) {
                $image = $files[$index]['image']->store('menu/variants', 'public');
            }

            if (!$image) {
                continue;
            }

            $variants[] = [
                'flavor' => $flavor,
                'size' => $size,
                'image' => $image,
            ];
        }

        return $variants;
    }

    private function parseNutritionInfo($input, array $fallback = []): array
    {
        if (is_array($input)) {
            return $input;
        }

        if (is_string($input)) {
            $trimmed = trim($input);
            if ($trimmed === '') {
                return $fallback;
            }

            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return $fallback;
    }
}

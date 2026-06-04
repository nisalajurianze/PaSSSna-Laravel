@extends('layouts.admin')

@section('title', 'Edit Menu Item')
@section('header', 'Edit Menu Item')

@section('content')
@php
    $allowedCategories = [
        'appetizer' => 'Appetizers',
        'main_course' => 'Main Course',
        'dessert' => 'Desserts',
        'beverage' => 'Drinks',
        'special' => 'Specials',
        'custom' => 'Custom',
    ];
    $normalizeCategory = function ($value) {
        return strtolower(trim(str_replace(['-', ' '], '_', $value ?? '')));
    };
    $categoryOptions = [];
    foreach ($allowedCategories as $value => $label) {
        $match = collect($categories ?? [])->first(function ($category) use ($normalizeCategory, $value) {
            $candidate = $category->slug ?? $category->name ?? '';
            return $normalizeCategory($candidate) === $value;
        });
        $categoryOptions[] = [
            'value' => $value,
            'label' => $match->name ?? $label,
        ];
    }

    $sizeGradients = [];
    if (is_array($item->nutrition_info ?? null)) {
        $sizeGradients = $item->nutrition_info['size_gradients'] ?? [];
    }

    $sizesRaw = old('sizes');
    if ($sizesRaw === null) {
        $sizesRaw = $item->sizes ?? [];
    }
    $sizes = [];
    if (is_array($sizesRaw)) {
        $isAssoc = array_keys($sizesRaw) !== range(0, count($sizesRaw) - 1);
        if ($isAssoc) {
            foreach ($sizesRaw as $name => $price) {
                $sizes[] = [
                    'name' => $name,
                    'price' => $price,
                    'gradient' => $sizeGradients[$name] ?? '',
                ];
            }
        } else {
            foreach ($sizesRaw as $size) {
                if (is_array($size)) {
                    $sizes[] = [
                        'name' => $size['name'] ?? '',
                        'price' => $size['price'] ?? ($item->price + ($size['price_modifier'] ?? 0)),
                        'gradient' => $size['gradient'] ?? ($sizeGradients[$size['name'] ?? ''] ?? ''),
                    ];
                } elseif (is_string($size)) {
                    $sizes[] = [
                        'name' => $size,
                        'price' => $item->price,
                        'gradient' => $sizeGradients[$size] ?? '',
                    ];
                }
            }
        }
    }

    $flavorsRaw = old('flavors', is_array($item->flavors) ? $item->flavors : []);
    $flavors = [];
    if (is_array($flavorsRaw)) {
        $isAssoc = array_keys($flavorsRaw) !== range(0, count($flavorsRaw) - 1);
        if ($isAssoc) {
            foreach ($flavorsRaw as $name => $price) {
                $flavors[] = [
                    'name' => $name,
                    'price' => $price,
                    'color' => '#DC2626',
                    'image' => null,
                ];
            }
        } else {
            foreach ($flavorsRaw as $flavor) {
                if (is_array($flavor)) {
                    $flavors[] = [
                        'name' => $flavor['name'] ?? '',
                        'price' => $flavor['price'] ?? 0,
                        'color' => $flavor['color'] ?? '#DC2626',
                        'image' => $flavor['image'] ?? null,
                    ];
                } elseif (is_string($flavor)) {
                    $flavors[] = [
                        'name' => $flavor,
                        'price' => 0,
                        'color' => '#DC2626',
                        'image' => null,
                    ];
                }
            }
        }
    }

    $toppingsRaw = old('extra_toppings', is_array($item->extra_toppings) ? $item->extra_toppings : []);
    $toppings = [];
    if (is_array($toppingsRaw)) {
        $isAssoc = array_keys($toppingsRaw) !== range(0, count($toppingsRaw) - 1);
        if ($isAssoc) {
            foreach ($toppingsRaw as $name => $price) {
                $toppings[] = [
                    'name' => $name,
                    'price' => $price,
                ];
            }
        } else {
            foreach ($toppingsRaw as $topping) {
                if (is_array($topping)) {
                    $toppings[] = [
                        'name' => $topping['name'] ?? '',
                        'price' => $topping['price'] ?? 0,
                    ];
                } elseif (is_string($topping)) {
                    $toppings[] = [
                        'name' => $topping,
                        'price' => 0,
                    ];
                }
            }
        }
    }

    $variantRaw = old('variant_images');
    if ($variantRaw === null && is_array($item->nutrition_info ?? null)) {
        $variantRaw = $item->nutrition_info['variant_images'] ?? [];
    }
    $variantImages = [];
    if (is_array($variantRaw)) {
        foreach ($variantRaw as $variant) {
            if (is_array($variant)) {
                $variantImages[] = [
                    'flavor' => $variant['flavor'] ?? '',
                    'size' => $variant['size'] ?? '',
                    'image' => $variant['image'] ?? null,
                ];
            }
        }
    }

    $nutritionInfoText = old('nutrition_info');
    if ($nutritionInfoText === null) {
        $nutritionInfoText = $item->nutrition_info ? json_encode($item->nutrition_info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '';
    }
@endphp

<div class="space-y-6">
    <form action="{{ route('admin.menu.update', ['menu' => $item->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $item->name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $item->slug) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Leave blank to keep current">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                        <option value="">Select Category</option>
                        @foreach($categoryOptions as $category)
                            <option value="{{ $category['value'] }}" @selected(old('category', $item->category) == $category['value'])>{{ $category['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Food Type</label>
                    <select name="food_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="non_vegetarian" @selected(old('food_type', $item->food_type) == 'non_vegetarian')>Non-Vegetarian</option>
                        <option value="vegetarian" @selected(old('food_type', $item->food_type) == 'vegetarian')>Vegetarian</option>
                        <option value="vegan" @selected(old('food_type', $item->food_type) == 'vegan')>Vegan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Base Price *</label>
                    <input type="number" name="price" value="{{ old('price', $item->price) }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preparation Time (minutes)</label>
                    <input type="number" name="preparation_time" value="{{ old('preparation_time', $item->preparation_time) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Offer Price</label>
                    <input type="number" name="offer_price" value="{{ old('offer_price', $item->offer_price) }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Offer Valid From</label>
                    <input type="date" name="offer_valid_from" value="{{ old('offer_valid_from', optional($item->offer_valid_from)->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Offer Valid To</label>
                    <input type="date" name="offer_valid_to" value="{{ old('offer_valid_to', optional($item->offer_valid_to)->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Offer Valid Until</label>
                    <input type="date" name="offer_valid_until" value="{{ old('offer_valid_until', optional($item->offer_valid_until)->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg" required>{{ old('description', $item->description) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                    <input type="text" name="short_description" value="{{ old('short_description', $item->short_description) }}" maxlength="255" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Optional short summary for cards">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Update Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    @if($item->image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="w-24 h-24 object-cover rounded-lg">
                        </div>
                    @endif
                </div>
                <div class="md:col-span-2 flex flex-wrap gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_available" value="1" @checked(old('is_available', $item->is_available)) class="w-5 h-5 text-red-600 rounded focus:ring-red-500">
                        <span class="ml-2 text-gray-700">Available</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_fast_moving" value="1" @checked(old('is_fast_moving', $item->is_fast_moving)) class="w-5 h-5 text-red-600 rounded focus:ring-red-500">
                        <span class="ml-2 text-gray-700">Fast moving (Popular)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_recommended" value="1" @checked(old('is_recommended', $item->is_recommended)) class="w-5 h-5 text-red-600 rounded focus:ring-red-500">
                        <span class="ml-2 text-gray-700">Recommended</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_customizable" value="1" @checked(old('is_customizable', $item->is_customizable)) class="w-5 h-5 text-red-600 rounded focus:ring-red-500">
                        <span class="ml-2 text-gray-700">Customizable</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ordering and Sorting</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Min Order Qty</label>
                    <input type="number" name="min_order_qty" value="{{ old('min_order_qty', $item->min_order_qty ?? 1) }}" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Order Qty</label>
                    <input type="number" name="max_order_qty" value="{{ old('max_order_qty', $item->max_order_qty ?? 10) }}" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? 0) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Sizes and Pricing</h3>
                <button type="button" onclick="addSize()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">+ Add Size</button>
            </div>
            <div id="sizes-container" class="space-y-4">
                @foreach($sizes as $index => $size)
                <div class="size-item flex gap-4 items-start p-4 border rounded-lg">
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Size Name</label>
                            <input type="text" name="sizes[{{ $index }}][name]" value="{{ $size['name'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                            <input type="number" name="sizes[{{ $index }}][price]" value="{{ $size['price'] ?? 0 }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gradient (optional)</label>
                            <input type="text" name="sizes[{{ $index }}][gradient]" value="{{ $size['gradient'] ?? '' }}" placeholder="linear-gradient(135deg, #FBBF24, #DC2626)" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Flavors / Categories</h3>
                <button type="button" onclick="addFlavor()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">+ Add Flavor</button>
            </div>
            <div id="flavors-container" class="space-y-4">
                @foreach($flavors as $index => $flavor)
                <div class="flavor-item flex gap-4 items-start p-4 border rounded-lg">
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Flavor Name</label>
                            <input type="text" name="flavors[{{ $index }}][name]" value="{{ $flavor['name'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price Add-on</label>
                            <input type="number" name="flavors[{{ $index }}][price]" value="{{ $flavor['price'] ?? 0 }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <input type="color" name="flavors[{{ $index }}][color]" value="{{ $flavor['color'] ?? '#DC2626' }}" class="w-full h-11 px-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Flavor Image</label>
                            <input type="file" name="flavors[{{ $index }}][image]" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            @if(!empty($flavor['image']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $flavor['image']) }}" alt="Flavor image" class="w-20 h-20 object-cover rounded-lg">
                                    <input type="hidden" name="flavors[{{ $index }}][existing_image]" value="{{ $flavor['image'] }}">
                                </div>
                            @endif
                        </div>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Variant Photos (Flavor + Size)</h3>
                <button type="button" onclick="addVariant()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">+ Add Variant</button>
            </div>
            <p class="text-sm text-gray-500 mb-4">Add photos for specific flavor and size combinations (e.g., Banana + Medium).</p>
            <div id="variants-container" class="space-y-4">
                @foreach($variantImages as $index => $variant)
                <div class="variant-item flex gap-4 items-start p-4 border rounded-lg">
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Flavor</label>
                            <input type="text" name="variant_images[{{ $index }}][flavor]" value="{{ $variant['flavor'] ?? '' }}" placeholder="Banana" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Size</label>
                            <input type="text" name="variant_images[{{ $index }}][size]" value="{{ $variant['size'] ?? '' }}" placeholder="Medium" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                            <input type="file" name="variant_images[{{ $index }}][image]" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            @if(!empty($variant['image']))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $variant['image']) }}" alt="Variant image" class="w-20 h-20 object-cover rounded-lg">
                                    <input type="hidden" name="variant_images[{{ $index }}][existing_image]" value="{{ $variant['image'] }}">
                                </div>
                            @endif
                        </div>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Extra Toppings</h3>
                <button type="button" onclick="addTopping()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">+ Add Topping</button>
            </div>
            <div id="toppings-container" class="space-y-4">
                @foreach($toppings as $index => $topping)
                <div class="topping-item flex gap-4 items-start p-4 border rounded-lg">
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Topping Name</label>
                            <input type="text" name="extra_toppings[{{ $index }}][name]" value="{{ $topping['name'] ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                            <input type="number" name="extra_toppings[{{ $index }}][price]" value="{{ $topping['price'] ?? 0 }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ingredients</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ingredients (comma separated)</label>
                <textarea name="ingredients_text" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Chicken, Rice, Vegetables">{{ is_array($item->ingredients) ? implode(', ', $item->ingredients) : '' }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Nutrition Info (JSON)</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nutrition Info</label>
                <textarea name="nutrition_info" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder='{"calories": 250, "protein": "10g"}'>{{ $nutritionInfoText }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.menu.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Update Menu Item</button>
        </div>
    </form>
</div>

<script>
let flavorIndex = {{ count($flavors ?? []) }};
let sizeIndex = {{ count($sizes ?? []) }};
let toppingIndex = {{ count($toppings ?? []) }};
let variantIndex = {{ count($variantImages ?? []) }};

function addSize() {
    const container = document.getElementById('sizes-container');
    const html = `
        <div class="size-item flex gap-4 items-start p-4 border rounded-lg">
            <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Size Name</label>
                    <input type="text" name="sizes[${sizeIndex}][name]" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                    <input type="number" name="sizes[${sizeIndex}][price]" step="0.01" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gradient (optional)</label>
                    <input type="text" name="sizes[${sizeIndex}][gradient]" placeholder="linear-gradient(135deg, #FBBF24, #DC2626)" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
    sizeIndex++;
}

function addFlavor() {
    const container = document.getElementById('flavors-container');
    const html = `
        <div class="flavor-item flex gap-4 items-start p-4 border rounded-lg">
            <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Flavor Name</label>
                    <input type="text" name="flavors[${flavorIndex}][name]" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price Add-on</label>
                    <input type="number" name="flavors[${flavorIndex}][price]" step="0.01" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                    <input type="color" name="flavors[${flavorIndex}][color]" value="#DC2626" class="w-full h-11 px-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Flavor Image</label>
                    <input type="file" name="flavors[${flavorIndex}][image]" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
    flavorIndex++;
}

function addVariant() {
    const container = document.getElementById('variants-container');
    const html = `
        <div class="variant-item flex gap-4 items-start p-4 border rounded-lg">
            <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Flavor</label>
                    <input type="text" name="variant_images[${variantIndex}][flavor]" placeholder="Banana" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Size</label>
                    <input type="text" name="variant_images[${variantIndex}][size]" placeholder="Medium" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Photo</label>
                    <input type="file" name="variant_images[${variantIndex}][image]" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
    variantIndex++;
}

function addTopping() {
    const container = document.getElementById('toppings-container');
    const html = `
        <div class="topping-item flex gap-4 items-start p-4 border rounded-lg">
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Topping Name</label>
                    <input type="text" name="extra_toppings[${toppingIndex}][name]" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                    <input type="number" name="extra_toppings[${toppingIndex}][price]" step="0.01" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
        </div>`;
    container.insertAdjacentHTML('beforeend', html);
    toppingIndex++;
}
</script>
@endsection


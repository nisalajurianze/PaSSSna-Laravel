@extends('layouts.admin')

@section('title', 'Create Menu Item')
@section('header', 'Create Menu Item')

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
@endphp

<div class="space-y-6">
    <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Leave blank to auto-generate">
                    @error('slug')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" required>
                        <option value="">-- Select Category --</option>
                        @foreach($categoryOptions as $category)
                            <option value="{{ $category['value'] }}" {{ old('category') == $category['value'] ? 'selected' : '' }}>
                                {{ $category['label'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Food Type</label>
                    <select name="food_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="non_vegetarian" {{ old('food_type') == 'non_vegetarian' ? 'selected' : '' }}>Non-Vegetarian</option>
                        <option value="vegetarian" {{ old('food_type') == 'vegetarian' ? 'selected' : '' }}>Vegetarian</option>
                        <option value="vegan" {{ old('food_type') == 'vegan' ? 'selected' : '' }}>Vegan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Base Price *</label>
                    <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" required>
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preparation Time (minutes)</label>
                    <input type="number" name="preparation_time" value="{{ old('preparation_time') }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Offer Price</label>
                    <input type="number" name="offer_price" value="{{ old('offer_price') }}" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Offer Valid From</label>
                    <input type="date" name="offer_valid_from" value="{{ old('offer_valid_from') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Offer Valid To</label>
                    <input type="date" name="offer_valid_to" value="{{ old('offer_valid_to') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Offer Valid Until</label>
                    <input type="date" name="offer_valid_until" value="{{ old('offer_valid_until') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                    <input type="text" name="short_description" value="{{ old('short_description') }}" maxlength="255" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Optional short summary for cards">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    @error('image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2 flex flex-wrap gap-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_available" value="1" {{ old('is_available', true) ? 'checked' : '' }} class="w-5 h-5 text-red-600 rounded focus:ring-red-500">
                        <span class="ml-2 text-gray-700">Available for ordering</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_fast_moving" value="1" {{ old('is_fast_moving') ? 'checked' : '' }} class="w-5 h-5 text-red-600 rounded focus:ring-red-500">
                        <span class="ml-2 text-gray-700">Fast moving (Popular)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_recommended" value="1" {{ old('is_recommended') ? 'checked' : '' }} class="w-5 h-5 text-red-600 rounded focus:ring-red-500">
                        <span class="ml-2 text-gray-700">Recommended</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_customizable" value="1" {{ old('is_customizable') ? 'checked' : '' }} class="w-5 h-5 text-red-600 rounded focus:ring-red-500">
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
                    <input type="number" name="min_order_qty" value="{{ old('min_order_qty', 1) }}" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Order Qty</label>
                    <input type="number" name="max_order_qty" value="{{ old('max_order_qty', 10) }}" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Sizes and Pricing</h3>
                <button type="button" onclick="addSize()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">+ Add Size</button>
            </div>
            <div id="sizes-container" class="space-y-4">
                @if(old('sizes'))
                    @foreach(old('sizes') as $index => $size)
                    @php
                        $sizeName = is_array($size) ? ($size['name'] ?? '') : $size;
                        $sizePrice = is_array($size) ? ($size['price'] ?? 0) : 0;
                        $sizeGradient = is_array($size) ? ($size['gradient'] ?? '') : '';
                    @endphp
                    <div class="size-item flex gap-4 items-start p-4 border rounded-lg">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Size Name</label>
                                <input type="text" name="sizes[{{ $index }}][name]" value="{{ $sizeName }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                                <input type="number" name="sizes[{{ $index }}][price]" value="{{ $sizePrice }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gradient (optional)</label>
                                <input type="text" name="sizes[{{ $index }}][gradient]" value="{{ $sizeGradient }}" placeholder="linear-gradient(135deg, #FBBF24, #DC2626)" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Flavors / Categories</h3>
                <button type="button" onclick="addFlavor()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">+ Add Flavor</button>
            </div>
            <div id="flavors-container" class="space-y-4">
                @if(old('flavors'))
                    @foreach(old('flavors') as $index => $flavor)
                    @php
                        $flavorName = is_array($flavor) ? ($flavor['name'] ?? '') : $flavor;
                        $flavorPrice = is_array($flavor) ? ($flavor['price'] ?? 0) : 0;
                        $flavorColor = is_array($flavor) ? ($flavor['color'] ?? '#DC2626') : '#DC2626';
                    @endphp
                    <div class="flavor-item flex gap-4 items-start p-4 border rounded-lg">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Flavor Name</label>
                                <input type="text" name="flavors[{{ $index }}][name]" value="{{ $flavorName }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price Add-on</label>
                                <input type="number" name="flavors[{{ $index }}][price]" value="{{ $flavorPrice }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                                <input type="color" name="flavors[{{ $index }}][color]" value="{{ $flavorColor }}" class="w-full h-11 px-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Flavor Image</label>
                                <input type="file" name="flavors[{{ $index }}][image]" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Variant Photos (Flavor + Size)</h3>
                <button type="button" onclick="addVariant()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">+ Add Variant</button>
            </div>
            <p class="text-sm text-gray-500 mb-4">Add photos for specific flavor and size combinations (e.g., Banana + Medium).</p>
            <div id="variants-container" class="space-y-4">
                @if(old('variant_images'))
                    @foreach(old('variant_images') as $index => $variant)
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
                            </div>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Extra Toppings</h3>
                <button type="button" onclick="addTopping()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">+ Add Topping</button>
            </div>
            <div id="toppings-container" class="space-y-4">
                @if(old('extra_toppings'))
                    @foreach(old('extra_toppings') as $index => $topping)
                    @php
                        $toppingName = is_array($topping) ? ($topping['name'] ?? '') : $topping;
                        $toppingPrice = is_array($topping) ? ($topping['price'] ?? 0) : 0;
                    @endphp
                    <div class="topping-item flex gap-4 items-start p-4 border rounded-lg">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Topping Name</label>
                                <input type="text" name="extra_toppings[{{ $index }}][name]" value="{{ $toppingName }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                                <input type="number" name="extra_toppings[{{ $index }}][price]" value="{{ $toppingPrice }}" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ingredients</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ingredients (comma-separated)</label>
                <textarea name="ingredients_text" rows="3" placeholder="e.g., Chicken, Rice, Spices" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">{{ old('ingredients_text') }}</textarea>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Nutrition Info (JSON)</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nutrition Info</label>
                <textarea name="nutrition_info" rows="4" placeholder='{"calories": 250, "protein": "10g"}' class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">{{ old('nutrition_info') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.menu.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">Cancel</a>
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Create Menu Item</button>
        </div>
    </form>
</div>

<script>
let sizeIndex = {{ old('sizes') ? count(old('sizes')) : 0 }};
let toppingIndex = {{ old('extra_toppings') ? count(old('extra_toppings')) : 0 }};
let flavorIndex = {{ old('flavors') ? count(old('flavors')) : 0 }};
let variantIndex = {{ old('variant_images') ? count(old('variant_images')) : 0 }};

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
        </div>
    `;
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
        </div>
    `;
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
        </div>
    `;
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
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    toppingIndex++;
}
</script>
@endsection


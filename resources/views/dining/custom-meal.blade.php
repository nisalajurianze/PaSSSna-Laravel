@extends('layouts.app', ['kiosk' => true])

@section('title', 'Custom Meal Builder')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#F8F3ED] via-white to-[#F3EEE7]">
    <div class="px-4 py-6 lg:px-10">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-sm text-gray-500">Table #{{ $session->table_number }}</p>
                <h1 class="text-2xl font-bold text-gray-800">Custom Meal Builder</h1>
            </div>
            <a href="{{ route('dining.menu') }}" class="px-4 py-2 rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Menu
            </a>
        </div>

        <div x-data="customMealBuilder()" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <form method="POST" action="{{ route('dining.custom.add') }}" class="lg:col-span-2 space-y-6">
                @csrf
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">1. Choose Your Base</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($bases as $base)
                            <label class="flex items-center gap-3 border border-gray-200 rounded-xl p-3 cursor-pointer hover:border-primary-red">
                                <input type="radio" name="base" value="{{ $base->id }}" x-model="baseId" class="text-primary-red" required>
                                <div>
                                    <p class="font-semibold text-gray-700">{{ $base->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $base->description }}</p>
                                </div>
                                <span class="ml-auto text-sm font-semibold text-primary-red">
                                    {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($base->price, 2) }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">2. Choose Your Meat / Protein</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($proteins as $protein)
                            <label class="flex items-center gap-3 border border-gray-200 rounded-xl p-3 cursor-pointer hover:border-primary-red">
                                <input type="radio" name="protein" value="{{ $protein->id }}" x-model="proteinId" class="text-primary-red" required>
                                <div>
                                    <p class="font-semibold text-gray-700">{{ $protein->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $protein->description }}</p>
                                </div>
                                <span class="ml-auto text-sm font-semibold text-primary-red">
                                    {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($protein->price, 2) }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">3. Choose Vegetables</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($vegetables as $veg)
                            <label class="flex items-center gap-3 border border-gray-200 rounded-xl p-3 cursor-pointer hover:border-primary-red">
                                <input type="checkbox" name="vegetables[]" value="{{ $veg->id }}" x-model="vegetables" class="text-primary-red">
                                <div>
                                    <p class="font-semibold text-gray-700">{{ $veg->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $veg->description }}</p>
                                </div>
                                <span class="ml-auto text-sm font-semibold text-primary-red">
                                    {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($veg->price, 2) }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">4. Add-ons</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($addons as $addon)
                            <label class="flex items-center gap-3 border border-gray-200 rounded-xl p-3 cursor-pointer hover:border-primary-red">
                                <input type="checkbox" name="addons[]" value="{{ $addon->id }}" x-model="addons" class="text-primary-red">
                                <div>
                                    <p class="font-semibold text-gray-700">{{ $addon->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $addon->description }}</p>
                                </div>
                                <span class="ml-auto text-sm font-semibold text-primary-red">
                                    {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($addon->price, 2) }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">5. Final Touch</h2>
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <label class="text-sm text-gray-500">Special Instructions</label>
                            <textarea name="special_instructions" rows="2"
                                      class="w-full mt-2 px-3 py-2 border border-gray-200 rounded-xl"></textarea>
                        </div>
                        <div class="w-full md:w-40">
                            <label class="text-sm text-gray-500">Quantity</label>
                            <input type="number" name="quantity" min="1" max="10" value="1"
                                   class="w-full mt-2 px-3 py-2 border border-gray-200 rounded-xl">
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-primary-red text-white font-semibold hover:bg-red-700 transition">
                    Add Custom Meal to Order
                </button>
            </form>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-fit sticky top-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Ingredient List</h2>
                <ul class="space-y-2 text-sm">
                    <template x-for="ingredient in selectedIngredients" :key="ingredient.id">
                        <li class="flex items-center justify-between border-b border-gray-100 pb-2">
                            <span x-text="ingredient.name"></span>
                            <span class="text-primary-red font-semibold" x-text="formatPrice(ingredient.price)"></span>
                        </li>
                    </template>
                </ul>

                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-sm text-gray-500">Estimated Total</span>
                    <span class="text-lg font-semibold text-gray-800" x-text="formatPrice(totalPrice)"></span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function customMealBuilder() {
        const data = {
            bases: @json($bases->values()),
            proteins: @json($proteins->values()),
            vegetables: @json($vegetables->values()),
            addons: @json($addons->values()),
        };

        const findById = (collection, id) => collection.find(item => Number(item.id) === Number(id));

        return {
            baseId: data.bases[0]?.id ?? null,
            proteinId: data.proteins[0]?.id ?? null,
            vegetables: [],
            addons: [],
            get selectedIngredients() {
                const selected = [];
                if (this.baseId) {
                    const base = findById(data.bases, this.baseId);
                    if (base) selected.push(base);
                }
                if (this.proteinId) {
                    const protein = findById(data.proteins, this.proteinId);
                    if (protein) selected.push(protein);
                }
                this.vegetables.forEach(id => {
                    const veg = findById(data.vegetables, id);
                    if (veg) selected.push(veg);
                });
                this.addons.forEach(id => {
                    const addon = findById(data.addons, id);
                    if (addon) selected.push(addon);
                });
                return selected;
            },
            get totalPrice() {
                return this.selectedIngredients.reduce((sum, item) => sum + Number(item.price || 0), 0);
            },
            formatPrice(value) {
                const symbol = @json(config('restaurant.payment.currency_symbol', 'LKR '));
                return symbol + Number(value || 0).toFixed(2);
            }
        };
    }
</script>
@endsection

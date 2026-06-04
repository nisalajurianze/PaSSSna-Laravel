<?php

namespace App\Http\Controllers\Dining;

use App\Http\Controllers\Controller;
use App\Models\CustomIngredient;
use App\Models\DiningSession;
use App\Models\Inventory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class DiningController extends Controller
{
    public function loginForm()
    {
        $tables = Table::orderBy('table_number')->get();
        $activeTables = DiningSession::where('status', DiningSession::STATUS_ACTIVE)
            ->pluck('table_number')
            ->all();

        return view('dining.login', compact('tables', 'activeTables'));
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'table_number' => 'required|integer|exists:tables,table_number',
            'guests' => 'nullable|integer|min:1|max:20',
        ]);

        $table = Table::where('table_number', $validated['table_number'])->first();

        if (!$table || !$table->is_active) {
            return back()->with('error', 'This table is not available right now.');
        }

        if (in_array($table->status, [Table::STATUS_MAINTENANCE, Table::STATUS_CLEANING], true)) {
            return back()->with('error', 'This table is currently unavailable.');
        }

        $session = DiningSession::where('table_number', $table->table_number)
            ->where('status', DiningSession::STATUS_ACTIVE)
            ->latest('start_time')
            ->first();

        if (!$session) {
            $session = DiningSession::create([
                'session_code' => DiningSession::generateSessionCode(),
                'table_number' => $table->table_number,
                'number_of_people' => $validated['guests'] ?? 1,
                'status' => DiningSession::STATUS_ACTIVE,
                'start_time' => now(),
            ]);
        }

        $table->update(['status' => Table::STATUS_OCCUPIED]);

        Session::put('dining_session_token', $session->session_code);
        Session::put('dining_table_number', $table->table_number);
        Session::forget('dining_cart');

        return redirect()->route('dining.menu')->with('success', "Welcome to Table {$table->table_number}!");
    }

    public function menu(Request $request)
    {
        $session = $this->currentSession($request);

        $query = MenuItem::where('is_available', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('ingredients', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $menuItems = $query->orderBy('name')->paginate(12)->withQueryString();

        $categories = MenuItem::where('is_available', true)
            ->distinct()
            ->pluck('category');

        $recommendations = $this->getRecommendations();

        $cart = $this->getCart();
        $subtotal = $this->getCartTotal($cart);
        $taxRate = (float) config('restaurant.order.tax_rate', 0);
        $serviceRate = (float) config('restaurant.order.service_charge_rate', 0);
        $tax = $subtotal * ($taxRate / 100);
        $serviceCharge = $subtotal * ($serviceRate / 100);
        $total = $subtotal + $tax + $serviceCharge;

        $orders = Order::where('dining_session_id', $session->id)
            ->latest()
            ->with('items')
            ->get();

        return view('dining.menu', compact(
            'session',
            'menuItems',
            'categories',
            'recommendations',
            'cart',
            'subtotal',
            'tax',
            'serviceCharge',
            'total',
            'orders'
        ));
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1|max:10',
            'size' => 'nullable|string',
            'flavor' => 'nullable|string',
            'toppings' => 'nullable|array',
            'special_instructions' => 'nullable|string|max:500',
        ]);

        $item = MenuItem::where('is_available', true)->findOrFail($validated['item_id']);

        $price = $this->calculateMenuPrice($item, $validated);

        $cart = $this->getCart();
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
                'size' => $validated['size'] ?? null,
                'flavor' => $validated['flavor'] ?? null,
                'toppings' => $validated['toppings'] ?? [],
                'special_instructions' => $validated['special_instructions'] ?? null,
                'image' => $item->image_url,
                'total' => $price * $validated['quantity'],
                'is_custom_meal' => false,
            ];
        }

        $this->saveCart($cart);

        return back()->with('success', "{$item->name} added to your table order.");
    }

    public function updateCart(Request $request, string $key)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cart = $this->getCart();

        if (!isset($cart[$key])) {
            return back()->with('error', 'Item not found in the order.');
        }

        $cart[$key]['quantity'] = $validated['quantity'];
        $cart[$key]['total'] = $cart[$key]['quantity'] * $cart[$key]['price'];

        $this->saveCart($cart);

        return back()->with('success', 'Order updated.');
    }

    public function removeCartItem(string $key)
    {
        $cart = $this->getCart();

        if (isset($cart[$key])) {
            unset($cart[$key]);
            $this->saveCart($cart);
        }

        return back()->with('success', 'Item removed from the order.');
    }

    public function clearCart()
    {
        Session::forget('dining_cart');
        return back()->with('success', 'Table order cleared.');
    }

    public function customMealForm(Request $request)
    {
        $session = $this->currentSession($request);

        $bases = CustomIngredient::where('category', 'base')->where('is_available', true)->orderBy('sort_order')->get();
        $proteins = CustomIngredient::where('category', 'protein')->where('is_available', true)->orderBy('sort_order')->get();
        $vegetables = CustomIngredient::where('category', 'vegetable')->where('is_available', true)->orderBy('sort_order')->get();
        $addons = CustomIngredient::whereIn('category', ['sauce', 'topping', 'cheese', 'spice'])
            ->where('is_available', true)
            ->orderBy('sort_order')
            ->get();

        return view('dining.custom-meal', compact('session', 'bases', 'proteins', 'vegetables', 'addons'));
    }

    public function addCustomMeal(Request $request)
    {
        $validated = $request->validate([
            'base' => 'required|exists:custom_ingredients,id',
            'protein' => 'required|exists:custom_ingredients,id',
            'vegetables' => 'nullable|array',
            'vegetables.*' => 'exists:custom_ingredients,id',
            'addons' => 'nullable|array',
            'addons.*' => 'exists:custom_ingredients,id',
            'quantity' => 'required|integer|min:1|max:10',
            'special_instructions' => 'nullable|string|max:500',
        ]);

        $base = CustomIngredient::where('id', $validated['base'])->where('category', 'base')->firstOrFail();
        $protein = CustomIngredient::where('id', $validated['protein'])->where('category', 'protein')->firstOrFail();
        $vegetables = CustomIngredient::whereIn('id', $validated['vegetables'] ?? [])
            ->where('category', 'vegetable')
            ->get();
        $addons = CustomIngredient::whereIn('id', $validated['addons'] ?? [])
            ->whereIn('category', ['sauce', 'topping', 'cheese', 'spice'])
            ->get();

        $selected = collect([$base, $protein])->merge($vegetables)->merge($addons);
        $price = (float) $selected->sum('price');

        $description = 'Base: ' . $base->name . ' | Protein: ' . $protein->name;
        if ($vegetables->count() > 0) {
            $description .= ' | Veggies: ' . $vegetables->pluck('name')->implode(', ');
        }
        if ($addons->count() > 0) {
            $description .= ' | Add-ons: ' . $addons->pluck('name')->implode(', ');
        }

        $cartKey = $this->generateCustomKey($base, $protein, $vegetables, $addons, $validated['special_instructions'] ?? null);
        $cart = $this->getCart();

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $validated['quantity'];
            $cart[$cartKey]['total'] = $cart[$cartKey]['quantity'] * $cart[$cartKey]['price'];
        } else {
            $cart[$cartKey] = [
                'item_id' => null,
                'name' => 'Custom Meal',
                'description' => $description,
                'quantity' => $validated['quantity'],
                'price' => $price,
                'total' => $price * $validated['quantity'],
                'custom_ingredients' => $selected->map(function (CustomIngredient $ingredient) {
                    return [
                        'id' => $ingredient->id,
                        'name' => $ingredient->name,
                        'category' => $ingredient->category,
                        'price' => (float) $ingredient->price,
                    ];
                })->values()->all(),
                'special_instructions' => $validated['special_instructions'] ?? null,
                'is_custom_meal' => true,
            ];
        }

        $this->saveCart($cart);

        return redirect()->route('dining.menu')->with('success', 'Custom meal added to your table order.');
    }

    public function placeOrder(Request $request)
    {
        $session = $this->currentSession($request);
        $cart = $this->getCart();

        if (empty($cart)) {
            return back()->with('error', 'Your table order is empty.');
        }

        $taxRate = (float) config('restaurant.order.tax_rate', 0);
        $serviceRate = (float) config('restaurant.order.service_charge_rate', 0);

        $subtotal = $this->getCartTotal($cart);
        $tax = $subtotal * ($taxRate / 100);
        $serviceCharge = $subtotal * ($serviceRate / 100);
        $total = $subtotal + $tax + $serviceCharge;

        DB::beginTransaction();

        try {
            $order = Order::create([
                'order_number' => Order::generateUniqueOrderNumber(),
                'user_id' => null,
                'order_type' => Order::TYPE_DINE_IN,
                'table_number' => $session->table_number,
                'dining_session_id' => $session->id,
                'status' => Order::STATUS_PENDING,
                'payment_status' => 'pending',
                'payment_method' => Order::PAYMENT_CASH,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'delivery_charge' => 0,
                'discount_amount' => 0,
                'total' => $total,
                'total_amount' => $total,
                'customer_name' => 'Table ' . $session->table_number,
                'customer_phone' => 'N/A',
                'customer_email' => null,
                'special_instructions' => $request->input('special_instructions'),
                'estimated_preparation_time' => (int) config('restaurant.order.preparation_time_default', 30),
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['item_id'] ?? null,
                    'is_custom_meal' => (bool) ($item['is_custom_meal'] ?? false),
                    'item_name' => $item['name'] ?? 'Custom Meal',
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['total'],
                    'size' => $item['size'] ?? null,
                    'flavor' => $item['flavor'] ?? null,
                    'selected_toppings' => $item['toppings'] ?? [],
                    'custom_ingredients' => $item['custom_ingredients'] ?? null,
                    'special_instructions' => $item['special_instructions'] ?? null,
                ]);

                if (!empty($item['item_id'])) {
                    MenuItem::where('id', $item['item_id'])->increment('total_orders', $item['quantity']);
                }
            }

            $session->update([
                'last_order_time' => now(),
                'total_bill' => (float) $session->total_bill + $total,
                'remaining_balance' => (float) $session->remaining_balance + $total,
            ]);

            Session::forget('dining_cart');

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Unable to place the order. Please try again.');
        }

        return back()->with('success', 'Order sent to the kitchen!');
    }

    public function closeSession(Request $request)
    {
        $validated = $request->validate([
            'admin_password' => 'required|string',
        ]);

        if (!$this->verifyAdminPassword($validated['admin_password'])) {
            return back()->with('error', 'Invalid admin password.');
        }

        $session = DiningSession::where('session_code', Session::get('dining_session_token'))
            ->where('status', DiningSession::STATUS_ACTIVE)
            ->first();

        if ($session) {
            $session->update([
                'status' => DiningSession::STATUS_CLOSED,
                'end_time' => now(),
            ]);

            Table::where('table_number', $session->table_number)
                ->update(['status' => Table::STATUS_AVAILABLE]);
        }

        Session::forget(['dining_session_token', 'dining_table_number', 'dining_cart']);

        return redirect()->route('dining.login')->with('success', 'Table closed successfully.');
    }

    public function updates(Request $request)
    {
        $lastCheck = $request->get('last_check');
        $timestamp = now();

        if ($lastCheck) {
            $lastCheck = \Carbon\Carbon::parse($lastCheck);
        } else {
            $lastCheck = now()->subMinutes(5);
        }

        $menuUpdated = MenuItem::where('updated_at', '>', $lastCheck)
            ->orWhere('created_at', '>', $lastCheck)
            ->exists();

        $recommendationsUpdated = MenuItem::where('is_recommended', true)
            ->where('updated_at', '>', $lastCheck)
            ->exists();

        $stockUpdated = Inventory::where('updated_at', '>', $lastCheck)->exists();

        return response()->json([
            'menu_updated' => $menuUpdated,
            'recommendations_updated' => $recommendationsUpdated,
            'stock_updated' => $stockUpdated,
            'timestamp' => $timestamp->toIso8601String(),
        ]);
    }

    private function currentSession(Request $request): DiningSession
    {
        $session = $request->attributes->get('dining_session');

        if ($session instanceof DiningSession) {
            return $session;
        }

        return DiningSession::where('session_code', Session::get('dining_session_token'))
            ->where('status', DiningSession::STATUS_ACTIVE)
            ->firstOrFail();
    }

    private function getRecommendations()
    {
        $recommended = MenuItem::where('is_available', true)
            ->where('is_recommended', true)
            ->latest()
            ->limit(6)
            ->get();

        if ($recommended->count() < 6) {
            $fallback = MenuItem::where('is_available', true)
                ->orderByDesc('total_orders')
                ->limit(6)
                ->get();
            $recommended = $recommended->merge($fallback)->unique('id')->take(6);
        }

        return $recommended;
    }

    private function getCart(): array
    {
        return Session::get('dining_cart', []);
    }

    private function saveCart(array $cart): void
    {
        Session::put('dining_cart', $cart);
    }

    private function getCartTotal(array $cart): float
    {
        return array_sum(array_map(function ($item) {
            return (float) ($item['total'] ?? 0);
        }, $cart));
    }

    private function generateCartKey(int $itemId, array $options): string
    {
        $key = $itemId;
        $key .= '_' . ($options['size'] ?? 'regular');
        $key .= '_' . ($options['flavor'] ?? 'default');

        if (!empty($options['toppings'])) {
            $toppings = $options['toppings'];
            sort($toppings);
            $key .= '_' . implode(',', $toppings);
        }

        return md5($key);
    }

    private function generateCustomKey(CustomIngredient $base, CustomIngredient $protein, $vegetables, $addons, ?string $instructions): string
    {
        $payload = [
            'base' => $base->id,
            'protein' => $protein->id,
            'vegetables' => collect($vegetables)->pluck('id')->sort()->values()->all(),
            'addons' => collect($addons)->pluck('id')->sort()->values()->all(),
            'instructions' => $instructions,
        ];

        return md5(json_encode($payload));
    }

    private function calculateMenuPrice(MenuItem $item, array $options): float
    {
        $price = $item->current_price ?? $item->price;

        if (!empty($options['size']) && is_array($item->sizes)) {
            if (isset($item->sizes[$options['size']]) && is_numeric($item->sizes[$options['size']])) {
                $price = $item->sizes[$options['size']];
            } else {
                foreach ($item->sizes as $sizeEntry) {
                    if (is_array($sizeEntry) && strcasecmp($sizeEntry['name'] ?? '', $options['size']) === 0) {
                        $price = isset($sizeEntry['price'])
                            ? (float) $sizeEntry['price']
                            : $item->price + (float) ($sizeEntry['price_modifier'] ?? 0);
                        break;
                    }
                }
            }
        }

        if (!empty($options['flavor']) && is_array($item->flavors)) {
            $flavorName = $options['flavor'];
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

        if (!empty($options['toppings'])) {
            foreach ($options['toppings'] as $topping) {
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
        }

        return (float) $price;
    }

    private function verifyAdminPassword(string $password): bool
    {
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            return false;
        }

        return Hash::check($password, $admin->password);
    }
}

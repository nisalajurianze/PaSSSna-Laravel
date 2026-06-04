<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuItemsSeeder extends Seeder
{
    private int $targetCount = 150;

    public function run(): void
    {
        // Replace demo menu with Sri Lankan + international menu (100 items)
        MenuItem::query()->forceDelete();

        $items = [];

        $items = array_merge($items, $this->sriLankanAppetizers());
        $items = array_merge($items, $this->sriLankanMains());
        $items = array_merge($items, $this->sriLankanDesserts());
        $items = array_merge($items, $this->sriLankanBeverages());
        $items = array_merge($items, $this->internationalMains());
        $items = array_merge($items, $this->internationalSpecials());

        if (count($items) < $this->targetCount) {
            $items = array_merge($items, $this->generateFillers($this->targetCount - count($items)));
        }

        foreach ($items as $index => $item) {
            $item['slug'] = Str::slug($item['name']);
            $item['sort_order'] = $index + 1;
            $item['is_available'] = $item['is_available'] ?? true;
            $item['is_fast_moving'] = $item['is_fast_moving'] ?? false;
            $item['is_recommended'] = $item['is_recommended'] ?? false;
            $item['is_customizable'] = $item['is_customizable'] ?? false;
            $item['food_type'] = $item['food_type'] ?? 'non_vegetarian';
            $item['preparation_time'] = $item['preparation_time'] ?? 15;
            $item['base_price'] = $item['base_price'] ?? 0;

            MenuItem::create($item);
        }
    }

    private function sriLankanAppetizers(): array
    {
        return [
            $this->appetizer('Fish Cutlet', 'Crispy fish and potato cutlets with chili sauce.', 450, 'non_vegetarian'),
            $this->appetizer('Chicken Cutlet', 'Golden chicken cutlets with spice herb mix.', 480, 'non_vegetarian'),
            $this->appetizer('Vegetable Cutlet', 'Mixed vegetable cutlets, lightly spiced.', 380, 'vegetarian'),
            $this->appetizer('Isso Vadai', 'Prawn fritters with curry leaves and green chili.', 520, 'non_vegetarian'),
            $this->appetizer('Seeni Sambol Bun', 'Soft bun stuffed with sweet onion sambol.', 350, 'vegetarian'),
            $this->appetizer('Mutton Rolls', 'Sri Lankan rolls with spiced mutton filling.', 620, 'non_vegetarian'),
            $this->appetizer('Parippu Vadai', 'Crispy lentil fritters with onion and chili.', 300, 'vegan'),
            $this->appetizer('Pol Roti Trio', 'Coconut roti with lunu miris and seeni sambol.', 450, 'vegetarian'),
            $this->appetizer('Chili Garlic Prawns', 'Sauteed prawns in garlic chili sauce.', 850, 'non_vegetarian'),
            $this->appetizer('Sri Lankan Devilled Cashews', 'Spicy roasted cashews with curry leaves.', 520, 'vegetarian'),
            $this->appetizer('Fish Patties', 'Mini patties stuffed with curried fish.', 420, 'non_vegetarian'),
            $this->appetizer('Cheese Kottu Bites', 'Crispy bite-sized roti with cheese and spice.', 580, 'vegetarian'),
            $this->appetizer('Coconut Sambol Nachos', 'Fusion nachos with spicy pol sambol.', 600, 'vegetarian'),
            $this->appetizer('Chicken Wings Lankan Style', 'Sticky chili-lime wings, Lankan twist.', 900, 'non_vegetarian'),
            $this->appetizer('Hot Butter Cuttlefish', 'Crispy cuttlefish tossed in hot butter sauce.', 900, 'non_vegetarian'),
        ];
    }

    private function sriLankanMains(): array
    {
        $items = [
            $this->main('Rice & Curry - Chicken', 'Steamed rice with chicken curry and 3 veggie sides.', 1250, 'non_vegetarian'),
            $this->main('Rice & Curry - Fish', 'Steamed rice with fish curry and 3 veggie sides.', 1350, 'non_vegetarian'),
            $this->main('Rice & Curry - Beef', 'Steamed rice with beef curry and 3 veggie sides.', 1450, 'non_vegetarian'),
            $this->main('Rice & Curry - Vegetable', 'Steamed rice with 4 seasonal veggie curries.', 980, 'vegetarian'),
            $this->main('Rice & Curry - Vegan', 'Rice with dhal, jackfruit, and 3 veg curries.', 950, 'vegan'),
            $this->main('Lamprais', 'Banana leaf wrapped rice with meat, egg, and sambol.', 1750, 'non_vegetarian'),
            $this->main('Chicken Biriyani', 'Fragrant basmati with spiced chicken and raita.', 1650, 'non_vegetarian'),
            $this->main('Beef Biriyani', 'Basmati biriyani with tender beef.', 1750, 'non_vegetarian'),
            $this->main('Vegetable Biriyani', 'Aromatic biriyani with seasonal vegetables.', 1250, 'vegetarian'),
            $this->main('Seafood Fried Rice', 'Wok fried rice with prawns, cuttlefish, fish.', 1650, 'non_vegetarian'),
            $this->main('Chicken Fried Rice', 'Classic fried rice with chicken and egg.', 1350, 'non_vegetarian'),
            $this->main('Vegetable Fried Rice', 'Stir-fried rice with mixed vegetables.', 1150, 'vegetarian'),
            $this->main('Nasi Goreng Sri Lankan', 'Spiced fried rice with chicken and egg.', 1450, 'non_vegetarian'),
            $this->main('Kottu Roti - Chicken', 'Signature chopped roti with chicken and veg.', 1450, 'non_vegetarian'),
            $this->main('Kottu Roti - Beef', 'Chopped roti with beef and spices.', 1550, 'non_vegetarian'),
            $this->main('Kottu Roti - Seafood', 'Chopped roti with prawns and cuttlefish.', 1750, 'non_vegetarian'),
            $this->main('Kottu Roti - Cheese', 'Creamy cheese kottu with veg.', 1400, 'vegetarian'),
            $this->main('Kottu Roti - Vegetable', 'Chopped roti with mixed vegetables.', 1200, 'vegetarian'),
            $this->main('String Hoppers Set', 'Red/white string hoppers with dhal and sambol.', 950, 'vegetarian'),
            $this->main('Hoppers Set (3)', 'Plain hoppers with seeni sambol and curry.', 850, 'vegetarian'),
            $this->main('Egg Hoppers Set (3)', 'Egg hoppers with lunu miris.', 980, 'non_vegetarian'),
            $this->main('Chicken Curry & Roti', 'Soft godamba roti with chicken curry.', 1150, 'non_vegetarian'),
            $this->main('Polos Curry & Rice', 'Young jackfruit curry with rice.', 980, 'vegan'),
            $this->main('Crab Curry & Rice', 'Rich Jaffna crab curry with rice.', 2200, 'non_vegetarian'),
            $this->main('Devilled Chicken', 'Wok tossed devilled chicken with onions.', 1350, 'non_vegetarian'),
            $this->main('Devilled Beef', 'Spicy devilled beef with peppers.', 1450, 'non_vegetarian'),
            $this->main('Devilled Fish', 'Devilled fish with crispy onions.', 1500, 'non_vegetarian'),
            $this->main('Black Pork Curry', 'Slow cooked pork curry with roasted spices.', 1550, 'non_vegetarian'),
            $this->main('Mutton Curry & Rice', 'Spiced mutton curry with rice.', 1750, 'non_vegetarian'),
            $this->main('Jaffna Spicy Prawn Curry', 'Northern style prawn curry with rice.', 1750, 'non_vegetarian'),
            $this->main('Dhal Curry & Rice', 'Creamy dhal curry with rice.', 880, 'vegetarian'),
            $this->main('Sri Lankan Chicken Curry', 'House chicken curry with spices and coconut.', 1200, 'non_vegetarian'),
            $this->main('Chicken Shawarma Kottu', 'Fusion shawarma kottu with garlic sauce.', 1600, 'non_vegetarian'),
            $this->main('Sri Lankan Seafood Platter', 'Mixed seafood with rice and sides.', 2300, 'non_vegetarian'),
            $this->main('Fish Rice Bowl', 'Fish curry with rice and sambol.', 1150, 'non_vegetarian'),
        ];

        return $items;
    }

    private function sriLankanDesserts(): array
    {
        return [
            $this->dessert('Watalappan', 'Coconut custard pudding with jaggery and spices.', 520),
            $this->dessert('Bibikkan', 'Coconut treacle cake with cashews.', 480),
            $this->dessert('Kiri Pani', 'Curd and kithul treacle.', 420),
            $this->dessert('Coconut Pancake', 'Pancake stuffed with sweet coconut.', 450),
            $this->dessert('Caramel Pudding', 'Smooth caramel custard.', 460),
            $this->dessert('Chocolate Biscuit Pudding', 'Classic Lankan pudding with chocolate.', 520),
            $this->dessert('Fruit Salad with Ice Cream', 'Tropical fruit salad with vanilla ice cream.', 650),
            $this->dessert('Coconut Milk Jelly', 'Light coconut jelly with honey syrup.', 390),
            $this->dessert('Kalu Dodol Slice', 'Sticky treacle dessert slice.', 350),
            $this->dessert('Pineapple Upside-Down Cake', 'Soft cake with caramel pineapple.', 520),
        ];
    }

    private function sriLankanBeverages(): array
    {
        $sizeGradients = [
            'Small' => 'linear-gradient(135deg, rgba(56,189,248,0.35), rgba(14,116,144,0.45))',
            'Medium' => 'linear-gradient(135deg, rgba(251,191,36,0.35), rgba(234,88,12,0.45))',
            'Large' => 'linear-gradient(135deg, rgba(244,63,94,0.35), rgba(190,24,93,0.45))',
        ];

        return [
            $this->drink('King Coconut', 'Fresh thambili served chilled.', 380, [
                $this->flavor('Classic', 0, null, '#F59E0B'),
                $this->flavor('Mint Splash', 50, null, '#10B981'),
                $this->flavor('Lime Twist', 60, null, '#22C55E'),
            ], $sizeGradients),
            $this->drink('Woodapple Juice', 'Creamy woodapple juice with jaggery.', 420, [
                $this->flavor('Traditional', 0, null, '#F97316'),
                $this->flavor('Cinnamon', 40, null, '#B45309'),
                $this->flavor('Vanilla', 50, null, '#EAB308'),
            ], $sizeGradients),
            $this->drink('Faluda', 'Rose syrup, basil seeds, ice cream and jelly.', 650, [
                $this->flavor('Rose', 0, null, '#F472B6'),
                $this->flavor('Strawberry', 60, null, '#FB7185'),
                $this->flavor('Mango', 70, null, '#FBBF24'),
            ], $sizeGradients),
            $this->drink('Iced Milk Tea', 'Chilled Ceylon milk tea.', 380, [
                $this->flavor('Classic', 0, null, '#B45309'),
                $this->flavor('Vanilla', 40, null, '#EAB308'),
                $this->flavor('Caramel', 50, null, '#F59E0B'),
            ], $sizeGradients),
            $this->drink('Lime Soda', 'Sparkling lime soda with mint.', 320, [
                $this->flavor('Salted Lime', 0, null, '#22C55E'),
                $this->flavor('Honey Lime', 40, null, '#FACC15'),
                $this->flavor('Ginger Lime', 50, null, '#F97316'),
            ], $sizeGradients),
            $this->drink('Ceylon Iced Tea', 'Brewed black tea with citrus.', 340, [
                $this->flavor('Lemon', 0, null, '#FACC15'),
                $this->flavor('Peach', 50, null, '#FB923C'),
                $this->flavor('Lychee', 60, null, '#FDE68A'),
            ], $sizeGradients),
            $this->drink('Mango Lassi', 'Creamy yogurt mango lassi.', 520, [
                $this->flavor('Classic', 0, null, '#FBBF24'),
                $this->flavor('Cardamom', 50, null, '#F59E0B'),
                $this->flavor('Pineapple', 60, null, '#FCD34D'),
            ], $sizeGradients),
            $this->drink('Chocolate Milkshake', 'Rich chocolate shake with whipped cream.', 650, [
                $this->flavor('Dark', 0, null, '#7C3AED'),
                $this->flavor('Hazelnut', 60, null, '#A16207'),
                $this->flavor('Mocha', 70, null, '#6B7280'),
            ], $sizeGradients),
            $this->drink('Fresh Orange Juice', 'Freshly squeezed orange juice.', 480, [
                $this->flavor('Classic', 0, null, '#F97316'),
                $this->flavor('Ginger', 40, null, '#F59E0B'),
                $this->flavor('Mint', 40, null, '#34D399'),
            ], $sizeGradients),
            $this->drink('Ceylon Coffee', 'Smooth Sri Lankan coffee.', 380, [
                $this->flavor('Classic', 0, null, '#6B7280'),
                $this->flavor('Vanilla', 50, null, '#F59E0B'),
                $this->flavor('Caramel', 60, null, '#F97316'),
            ], $sizeGradients),
            $this->drink('Passion Fruit Mojito', 'Passion fruit mojito with mint.', 580, [
                $this->flavor('Classic', 0, null, '#14B8A6'),
                $this->flavor('Berry', 70, null, '#FB7185'),
                $this->flavor('Coconut', 60, null, '#E2E8F0'),
            ], $sizeGradients),
            $this->drink('Watermelon Cooler', 'Chilled watermelon juice with lime.', 450, [
                $this->flavor('Classic', 0, null, '#F87171'),
                $this->flavor('Mint', 40, null, '#34D399'),
                $this->flavor('Ginger', 50, null, '#F59E0B'),
            ], $sizeGradients),
            $this->drink('Pineapple Smash', 'Pineapple juice with soda and mint.', 480, [
                $this->flavor('Classic', 0, null, '#FACC15'),
                $this->flavor('Chili Lime', 60, null, '#F97316'),
                $this->flavor('Basil', 50, null, '#22C55E'),
            ], $sizeGradients),
            $this->drink('Coconut Water', 'Pure chilled coconut water.', 300, [
                $this->flavor('Classic', 0, null, '#F8FAFC'),
                $this->flavor('Lime', 40, null, '#A3E635'),
                $this->flavor('Ginger', 50, null, '#F59E0B'),
            ], $sizeGradients),
            $this->drink('Iced Chocolate', 'Creamy iced chocolate.', 520, [
                $this->flavor('Classic', 0, null, '#7C3AED'),
                $this->flavor('Salted Caramel', 70, null, '#F59E0B'),
                $this->flavor('Mint Choco', 60, null, '#22C55E'),
            ], $sizeGradients),
        ];
    }

    private function internationalMains(): array
    {
        return [
            $this->main('Classic Beef Burger', 'Juicy beef patty with cheese and house sauce.', 1500, 'non_vegetarian'),
            $this->main('Crispy Chicken Burger', 'Crispy chicken with spicy mayo.', 1350, 'non_vegetarian'),
            $this->main('Grilled Chicken Sandwich', 'Grilled chicken, lettuce, and tomato.', 1200, 'non_vegetarian'),
            $this->main('Margherita Pizza', 'Classic pizza with mozzarella and basil.', 1800, 'vegetarian'),
            $this->main('Pepperoni Pizza', 'Pepperoni, cheese, and tomato sauce.', 2100, 'non_vegetarian'),
            $this->main('Veggie Supreme Pizza', 'Loaded veggie pizza with olives.', 1900, 'vegetarian'),
            $this->main('Chicken Alfredo Pasta', 'Creamy alfredo with grilled chicken.', 1750, 'non_vegetarian'),
            $this->main('Seafood Pasta', 'Prawns and calamari in tomato basil sauce.', 2100, 'non_vegetarian'),
            $this->main('Vegetable Lasagna', 'Layers of pasta, veg, and cheese.', 1650, 'vegetarian'),
            $this->main('Fish & Chips', 'Crispy fish with fries and tartar.', 1600, 'non_vegetarian'),
            $this->main('Chicken Caesar Salad', 'Romaine, chicken, and parmesan.', 1200, 'non_vegetarian'),
            $this->main('Greek Salad', 'Feta, olives, and fresh vegetables.', 980, 'vegetarian'),
            $this->main('Steak & Pepper Sauce', 'Grilled steak with pepper sauce.', 2600, 'non_vegetarian'),
            $this->main('Chicken Quesadilla', 'Cheesy quesadilla with chicken.', 1450, 'non_vegetarian'),
            $this->main('Spaghetti Bolognese', 'Classic beef bolognese sauce.', 1650, 'non_vegetarian'),
        ];
    }

    private function internationalSpecials(): array
    {
        return [
            $this->special('Chef’s Tasting Platter', 'Signature platter with 4 chef picks.', 3200, 'non_vegetarian'),
            $this->special('Seafood Feast', 'Crab, prawns, and fish served with rice.', 3500, 'non_vegetarian'),
            $this->special('Family Party Platter', 'Family size platter with sides.', 4200, 'non_vegetarian'),
            $this->special('Vegetarian Celebration Platter', 'Assorted veg mains and sides.', 2800, 'vegetarian'),
            $this->special('Grill House Combo', 'Steak, chicken, and sausages.', 3900, 'non_vegetarian'),
            $this->special('Island Spice Combo', 'Sri Lankan curry trio with rice.', 2600, 'non_vegetarian'),
            $this->special('Seafood Kottu Party', 'Large seafood kottu for sharing.', 3200, 'non_vegetarian'),
            $this->special('Biryani Feast', 'Chicken, beef, and veg biryani trio.', 3000, 'non_vegetarian'),
            $this->special('Street Food Platter', 'Rolls, cutlets, vadai, and sambol.', 2200, 'non_vegetarian'),
            $this->special('Sweet Finale Platter', 'Dessert platter with Lankan sweets.', 1800, 'vegetarian'),
        ];
    }

    private function appetizer(string $name, string $description, float $price, string $foodType): array
    {
        return [
            'name' => $name,
            'description' => $description,
            'short_description' => $description,
            'base_price' => $price,
            'category' => 'appetizer',
            'food_type' => $foodType,
            'preparation_time' => 10,
            'is_recommended' => true,
            'sizes' => json_encode(['regular' => $price, 'large' => min(900, $price + 150)]),
            'flavors' => json_encode(['Original', 'Spicy', 'Extra Garlic']),
        ];
    }

    private function main(string $name, string $description, float $price, string $foodType): array
    {
        return [
            'name' => $name,
            'description' => $description,
            'short_description' => $description,
            'base_price' => $price,
            'category' => 'main_course',
            'food_type' => $foodType,
            'preparation_time' => 18,
            'is_fast_moving' => true,
            'sizes' => json_encode(['regular' => $price, 'large' => min(2600, $price + 350)]),
            'flavors' => json_encode(['Classic', 'Spicy', 'Extra Garlic']),
        ];
    }

    private function dessert(string $name, string $description, float $price): array
    {
        return [
            'name' => $name,
            'description' => $description,
            'short_description' => $description,
            'base_price' => $price,
            'category' => 'dessert',
            'food_type' => 'vegetarian',
            'preparation_time' => 8,
            'sizes' => json_encode(['single' => $price, 'sharing' => min(800, $price + 200)]),
            'flavors' => json_encode(['Original', 'Caramel', 'Chocolate']),
        ];
    }

    private function drink(string $name, string $description, float $basePrice, array $flavors, array $sizeGradients): array
    {
        $sizes = [
            'Small' => $basePrice,
            'Medium' => min(900, $basePrice + 120),
            'Large' => min(900, $basePrice + 240),
        ];

        return [
            'name' => $name,
            'description' => $description,
            'short_description' => $description,
            'base_price' => $basePrice,
            'category' => 'beverage',
            'food_type' => 'vegetarian',
            'preparation_time' => 5,
            'is_customizable' => true,
            'sizes' => json_encode($sizes),
            'flavors' => json_encode($flavors),
            'nutrition_info' => [
                'size_gradients' => $sizeGradients,
            ],
        ];
    }

    private function special(string $name, string $description, float $price, string $foodType): array
    {
        return [
            'name' => $name,
            'description' => $description,
            'short_description' => $description,
            'base_price' => $price,
            'category' => 'special',
            'food_type' => $foodType,
            'preparation_time' => 25,
            'sizes' => json_encode(['regular' => $price]),
            'flavors' => json_encode(['Signature', 'Spicy', 'Extra Sauce']),
            'is_recommended' => true,
        ];
    }

    private function flavor(string $name, float $price, ?string $image, string $color): array
    {
        return [
            'name' => $name,
            'price' => $price,
            'image' => $image,
            'color' => $color,
        ];
    }

    private function generateFillers(int $count): array
    {
        $fillers = [];
        $categories = ['appetizer', 'main_course', 'dessert', 'beverage', 'special'];
        $counters = array_fill_keys($categories, 1);

        $appetizerNames = ['Lunu Miris Bites', 'Spiced Lentil Cubes', 'Chili Cheese Rotis', 'Pol Sambol Tart', 'Devilled Corn'];
        $mainNames = ['Island Curry Bowl', 'Coconut Chili Rice', 'Spiced Chicken Bowl', 'Herb Fish Plate', 'Seeni Sambol Rice'];
        $dessertNames = ['Kithul Treacle Pudding', 'Coconut Honey Tart', 'Golden Semolina Halwa', 'Mango Jelly Cup', 'Cashew Milk Pudding'];
        $beverageNames = ['Tropical Iced Tea', 'Ginger Lime Cooler', 'Citrus Basil Soda', 'Passion Lemon Fizz', 'Coconut Chill'];
        $specialNames = ['Chef Signature Combo', 'Island Feast Plate', 'Family Curry Platter', 'Seafood Celebration', 'Spice Market Special'];

        $flavorPalette = [
            ['name' => 'Classic', 'price' => 0, 'color' => '#DC2626'],
            ['name' => 'Mint', 'price' => 40, 'color' => '#10B981'],
            ['name' => 'Citrus', 'price' => 50, 'color' => '#F59E0B'],
        ];

        $sizeGradients = [
            'Small' => 'linear-gradient(135deg, rgba(56,189,248,0.35), rgba(14,116,144,0.45))',
            'Medium' => 'linear-gradient(135deg, rgba(251,191,36,0.35), rgba(234,88,12,0.45))',
            'Large' => 'linear-gradient(135deg, rgba(244,63,94,0.35), rgba(190,24,93,0.45))',
        ];

        while (count($fillers) < $count) {
            $category = $categories[array_rand($categories)];
            $index = $counters[$category]++;

            switch ($category) {
                case 'appetizer':
                    $name = $appetizerNames[array_rand($appetizerNames)] . " {$index}";
                    $price = $this->randBetween(300, 900);
                    $fillers[] = $this->appetizer($name, 'Signature Sri Lankan starter.', $price, 'vegetarian');
                    break;
                case 'main_course':
                    $name = $mainNames[array_rand($mainNames)] . " {$index}";
                    $price = $this->randBetween(900, 2600);
                    $fillers[] = $this->main($name, 'Hearty island-inspired main dish.', $price, 'non_vegetarian');
                    break;
                case 'dessert':
                    $name = $dessertNames[array_rand($dessertNames)] . " {$index}";
                    $price = $this->randBetween(350, 800);
                    $fillers[] = $this->dessert($name, 'Sweet Sri Lankan-inspired dessert.', $price);
                    break;
                case 'beverage':
                    $name = $beverageNames[array_rand($beverageNames)] . " {$index}";
                    $price = $this->randBetween(250, 900);
                    $flavors = array_map(function ($flavor) {
                        return $this->flavor($flavor['name'], $flavor['price'], null, $flavor['color']);
                    }, $flavorPalette);
                    $fillers[] = $this->drink($name, 'Refreshing island beverage.', $price, $flavors, $sizeGradients);
                    break;
                case 'special':
                default:
                    $name = $specialNames[array_rand($specialNames)] . " {$index}";
                    $price = $this->randBetween(1800, 4200);
                    $fillers[] = $this->special($name, 'Signature special curated by the chef.', $price, 'non_vegetarian');
                    break;
            }
        }

        return $fillers;
    }

    private function randBetween(int $min, int $max): int
    {
        return random_int($min, $max);
    }
}


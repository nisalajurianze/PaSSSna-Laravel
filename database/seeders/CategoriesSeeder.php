<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Appetizers', 'slug' => 'appetizers', 'sort_order' => 1],
            ['name' => 'Main Course', 'slug' => 'main-course', 'sort_order' => 2],
            ['name' => 'Desserts', 'slug' => 'desserts', 'sort_order' => 3],
            ['name' => 'Beverages', 'slug' => 'beverages', 'sort_order' => 4],
            ['name' => 'Specials', 'slug' => 'specials', 'sort_order' => 5],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'description' => 'Delicious ' . strtolower($category['name']),
                'sort_order' => $category['sort_order'],
                'is_active' => true,
            ]);
        }
    }
}

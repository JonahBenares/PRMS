<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Real categories
        $categories = [
            'Office Supplies',
            'IT Equipment',
            'Furniture',
            'Cleaning Materials',
            'Vehicles',
            'Tools & Hardware',
        ];

        foreach ($categories as $name) {
            Category::create([
                'category_name' => $name,
            ]);
        }

        // Fake categories for DataTable stress test
        Category::factory()->count(1500)->create();
    }
}

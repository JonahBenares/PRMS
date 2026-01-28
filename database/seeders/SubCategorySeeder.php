<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubCategory;
use App\Models\Category;

class SubCategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Office Supplies' => [
                'Bond Paper',
                'Ballpen',
                'Folders',
                'Stapler',
            ],
            'IT Equipment' => [
                'Laptop',
                'Desktop',
                'Printer',
                'Router',
            ],
            'Furniture' => [
                'Office Chair',
                'Table',
                'Cabinet',
            ],
            'Cleaning Materials' => [
                'Detergent',
                'Disinfectant',
                'Trash Bags',
            ],
        ];

        foreach ($data as $categoryName => $subCategories) {
            $category = Category::where('category_name', $categoryName)->first();

            if (!$category) continue;

            foreach ($subCategories as $sub) {
                SubCategory::create([
                    'sub_cat_name' => $sub,
                    'category_id'  => $category->id,
                ]);
            }
        }
    }
}

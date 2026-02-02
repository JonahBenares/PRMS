<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubCategory>
 */
class SubCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sub_cat_name' => $this->faker->words(2, true),
            'category_id'  => Category::inRandomOrder()->first()->id,
        ];
    }
}
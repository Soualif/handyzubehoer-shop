<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $slug = $this->faker->unique()->slug(2);

        return [
            'name' => ['en' => ucfirst($slug), 'de' => 'DE '.$slug, 'fr' => 'FR '.$slug, 'it' => 'IT '.$slug],
            'slug' => $slug,
        ];
    }
}

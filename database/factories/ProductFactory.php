<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $words = $this->faker->unique()->words(3, true);

        return [
            'name' => ['en' => ucfirst($words), 'de' => 'Hülle '.$words, 'fr' => 'Coque '.$words, 'it' => 'Cover '.$words],
            'description' => ['en' => 'A nice accessory.'],
            'slug' => str($words)->slug()->toString(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    /**
     * A product with one purchasable variant.
     */
    public function withVariant(int $price = 1990, int $stock = 10): static
    {
        return $this->has(ProductVariant::factory()->state(['price' => $price, 'stock' => $stock]), 'variants');
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'sku' => $this->faker->unique()->regexify('[A-Z0-9]{10}'),
            'additional_cost' => $this->faker->randomFloat(2, 0, 20),
            'stock_count' => $this->faker->numberBetween(0, 100),
        ];
    }
}

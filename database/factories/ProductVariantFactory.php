<?php

namespace Database\Factories;

use Illuminate\Support\Str;
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
            'name' => Str::title($this->faker->sentence(10)),
            'sku' => $this->faker->unique()->regexify('[A-Z0-9]{10}'),
            'additional_cost' => $this->faker->randomFloat(2, 0, 20),
            'stock_count' => $this->faker->numberBetween(0, 100),
        ];
    }
}

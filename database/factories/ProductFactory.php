<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_variant_id' => $this->faker->numberBetween(1, 25),
            'name' => Str::title($this->faker->sentence(3)),
            'description' => Str::title($this->faker->sentence),
            'price' => $this->faker->randomFloat(2, 10, 100),
        ];
    }
}

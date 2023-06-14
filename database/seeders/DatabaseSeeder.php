<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "Creating 25 dummy product_variants\n";
        ProductVariant::factory(25)->create();

        echo "Creating 1000 dummy products.\n";
        Product::factory(1000)->create();
    }
}

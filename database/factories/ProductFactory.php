<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $purchase_price = $this->faker->randomFloat(2, 10, 1000);
        $sale_price = $purchase_price * (1 + $this->faker->randomFloat(2, 0.1, 0.5));
        $profit_margin = (($sale_price - $purchase_price) / $purchase_price) * 100;

        return [
            'internal_code' => $this->faker->unique()->bothify('PROD-####'),
            'barcode' => $this->faker->unique()->ean13(),
            'commercial_name' => $this->faker->words(3, true),
            'technical_name' => $this->faker->words(4, true),
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
            'presentation' => $this->faker->randomElement(['unit', 'box', 'blister', 'bottle', 'package']),
            'primary_unit' => $this->faker->randomElement(['piece', 'box', 'bottle', 'package']),
            'secondary_unit' => $this->faker->randomElement(['piece', 'box', 'bottle', 'package']),
            'purchase_price' => $purchase_price,
            'sale_price' => $sale_price,
            'profit_margin' => $profit_margin,
            'status' => $this->faker->randomElement(['active', 'inactive', 'discontinued']),
            'minimum_stock' => $this->faker->numberBetween(5, 20),
            'maximum_stock' => $this->faker->numberBetween(100, 1000),
            'description' => $this->faker->paragraph(),
        ];
    }
}

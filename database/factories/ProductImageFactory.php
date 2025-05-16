<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'image_path' => 'products/default.jpg',
            'image_name' => 'default.jpg',
            'is_primary' => $this->faker->boolean(20),
            'order' => $this->faker->numberBetween(0, 10),
        ];
    }
}

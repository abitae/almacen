<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\SaleOrder;
use App\Models\SaleOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SaleOrderItem>
 */
class SaleOrderItemFactory extends Factory
{
    protected $model = SaleOrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 10);
        $unitPrice = $this->faker->randomFloat(2, 10, 1000);
        $subtotal = $quantity * $unitPrice;
        $discount = $this->faker->randomFloat(2, 0, $subtotal * 0.2);
        $total = $subtotal - $discount;

        return [
            'sale_order_id' => SaleOrder::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderItemFactory extends Factory
{
    protected $model = PurchaseOrderItem::class;

    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 100);
        $unit_price = $this->faker->randomFloat(2, 10, 1000);
        $total_price = $quantity * $unit_price;

        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'total_price' => $total_price,
            'received_quantity' => $this->faker->randomFloat(2, 0, $quantity),
            'notes' => $this->faker->sentence(),
        ];
    }
}

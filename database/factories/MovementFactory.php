<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\Location;
use App\Models\Movement;
use App\Models\Product;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovementFactory extends Factory
{
    protected $model = Movement::class;

    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 100);
        $unit_price = $this->faker->randomFloat(2, 10, 1000);
        $total_price = $quantity * $unit_price;

        return [
            'product_id' => Product::factory(),
            'batch_id' => Batch::factory(),
            'warehouse_id' => Warehouse::factory(),
            'location_id' => Location::factory(),
            'type' => $this->faker->randomElement(['entry', 'exit', 'transfer', 'adjustment']),
            'quantity' => $quantity,
            'unit_price' => $unit_price,
            'total_price' => $total_price,
            'reference_type' => $this->faker->randomElement(['purchase_order', 'sale', 'adjustment']),
            'reference_id' => $this->faker->numberBetween(1, 100),
            'notes' => $this->faker->sentence(),
            'user_id' => User::factory(),
        ];
    }
}

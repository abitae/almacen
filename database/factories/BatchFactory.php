<?php

namespace Database\Factories;

use App\Models\Batch;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class BatchFactory extends Factory
{
    protected $model = Batch::class;

    public function definition(): array
    {
        $manufacturing_date = $this->faker->dateTimeBetween('-1 year', 'now');
        $expiration_date = $this->faker->dateTimeBetween($manufacturing_date, '+2 years');
        $unit_price = $this->faker->randomFloat(2, 10, 1000);

        return [
            'product_id' => Product::factory(),
            'warehouse_id' => Warehouse::factory(),
            'batch_number' => $this->faker->unique()->bothify('BAT-####'),
            'manufacturing_date' => $manufacturing_date,
            'expiration_date' => $expiration_date,
            'quantity' => $this->faker->randomFloat(2, 10, 1000),
            'unit_price' => $unit_price,
            'status' => $this->faker->randomElement(['active', 'expired', 'depleted']),
            'notes' => $this->faker->sentence,
        ];
    }
}

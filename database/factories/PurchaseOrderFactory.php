<?php

namespace Database\Factories;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseOrderFactory extends Factory
{
    protected $model = PurchaseOrder::class;

    public function definition(): array
    {
        $order_date = $this->faker->dateTimeBetween('-1 month', 'now');
        $expected_delivery_date = $this->faker->dateTimeBetween($order_date, '+1 month');

        return [
            'supplier_id' => Supplier::factory(),
            'order_number' => $this->faker->unique()->bothify('PO-####'),
            'order_date' => $order_date,
            'expected_delivery_date' => $expected_delivery_date,
            'status' => $this->faker->randomElement(['pending', 'partial', 'completed', 'cancelled']),
            'total_amount' => $this->faker->randomFloat(2, 100, 10000),
            'notes' => $this->faker->sentence(),
            'user_id' => User::factory(),
        ];
    }
}

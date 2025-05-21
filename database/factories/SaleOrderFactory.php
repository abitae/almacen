<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\SaleOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SaleOrder>
 */
class SaleOrderFactory extends Factory
{
    protected $model = SaleOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'order_number' => 'SO-' . $this->faker->unique()->numberBetween(1000, 9999),
            'order_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'total_amount' => $this->faker->randomFloat(2, 100, 10000),
            'notes' => $this->faker->optional()->sentence(),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'partial', 'refunded']),
            'shipping_address' => $this->faker->address(),
            'billing_address' => $this->faker->address(),
        ];
    }
}

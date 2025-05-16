<?php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Warehouse',
            'code' => $this->faker->unique()->bothify('WH-####'),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->companyEmail(),
            'manager_name' => $this->faker->name(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}

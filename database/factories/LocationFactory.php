<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'warehouse_id' => Warehouse::factory(),
            'name' => $this->faker->words(2, true),
            'code' => $this->faker->bothify('LOC-###'),
            'type' => $this->faker->randomElement(['shelf', 'rack', 'cell', 'other']),
            'description' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}

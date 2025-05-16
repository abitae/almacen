<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->sentence(),
            'parent_id' => null,
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }

    public function child(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => Category::factory(),
            ];
        });
    }
}

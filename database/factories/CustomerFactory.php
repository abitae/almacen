<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentType = $this->faker->randomElement(['dni', 'ruc', 'passport', 'other']);
        $documentNumber = match($documentType) {
            'dni' => $this->faker->unique()->numerify('########'),
            'ruc' => $this->faker->unique()->numerify('###########'),
            'passport' => $this->faker->unique()->bothify('??#######'),
            default => $this->faker->unique()->numerify('##########'),
        };

        return [
            'user_id' => User::factory(),
            'document_type' => $documentType,
            'document_number' => $documentNumber,
            'name' => $this->faker->company(),
            'email' => $this->faker->unique()->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'country' => $this->faker->country(),
            'postal_code' => $this->faker->postcode(),
            'notes' => $this->faker->optional()->sentence(),
            'status' => $this->faker->boolean(90), // 90% de probabilidad de estar activo
        ];
    }
}

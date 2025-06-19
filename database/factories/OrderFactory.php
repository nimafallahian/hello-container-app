<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bl_release_date' => $this->faker->optional()->dateTime(),
            'bl_release_user_id' => $this->faker->optional()->randomElement(User::pluck('id')->toArray()),
            'freight_payer_self' => $this->faker->boolean(),
            'contract_number' => 'CTR-' . $this->faker->unique()->numberBetween(1000, 9999),
            'bl_number' => 'BL-' . $this->faker->unique()->numberBetween(100000, 999999),
        ];
    }
}

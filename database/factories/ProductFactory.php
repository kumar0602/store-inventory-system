<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory {
    public function definition(): array {
        return [
            'name' => $this->faker->words(2, true),
            'code' => strtoupper($this->faker->unique()->bothify('PRD-###')),
            'price_per_unit' => $this->faker->randomFloat(2, 10, 500),
            'tax_percentage' => 5.00,
            'stock_on_hand' => $this->faker->numberBetween(5, 50),
        ];
    }
}
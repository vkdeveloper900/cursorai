<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Test>
 */
class TestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'intro' => $this->faker->paragraph(),
            'instructions' => $this->faker->paragraph(),
            'difficulty' => $this->faker->randomElement(['easy', 'medium', 'hard']),
            'total_time' => $this->faker->numberBetween(10, 180), // minutes
            'configuration' => [],
            'status' => $this->faker->randomElement(['draft', 'published']),
        ];
    }
}

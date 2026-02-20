<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'section_id' => Section::factory(),
            'question_text' => $this->faker->sentence(),
            'question_type' => $this->faker->randomElement(['mcq', 'scale']),
            'difficulty' => $this->faker->randomElement(['easy', 'medium', 'hard']),
            'status' => $this->faker->boolean(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuestionOption>
 */
class QuestionOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
            'option_text' => $this->faker->word(),
            'score_value' => $this->faker->numberBetween(1, 10),
            'is_correct' => $this->faker->boolean(),
            'sequence' => $this->faker->numberBetween(1, 4),
        ];
    }
}

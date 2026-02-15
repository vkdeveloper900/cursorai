<?php

namespace App\Services\Test;

use App\Models\Test;
use App\Models\Question;
use Illuminate\Support\Collection;

class QuestionGeneratorService
{
    /**
     * Generate questions for a test
     */
    public function generate(Test $test): array
    {
        $finalQuestions = collect();

        foreach ($test->testSections as $testSection) {

            foreach ($testSection->rules as $rule) {

                $questions = Question::where('section_id', $testSection->section_id)
                    ->where('difficulty', $rule->difficulty)
                    ->where('status', 1)
                    ->inRandomOrder()
                    ->limit($rule->question_count)
                    ->pluck('id');

                // Safety check
                if ($questions->count() < $rule->question_count) {
                    throw new \Exception(
                        "Not enough {$rule->difficulty} questions in section {$testSection->section->name}"
                    );
                }

                $finalQuestions = $finalQuestions->merge($questions);
            }
        }

        return $finalQuestions->shuffle()->values()->toArray();
    }
}

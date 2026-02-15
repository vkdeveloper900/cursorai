<?php

namespace App\Services\User\Test;

use App\Models\Question;
use App\Models\Test;
use App\Services\Test\QuestionGeneratorService;

class TestQuestionService
{
    /**
     * Return user-friendly questions for a test
     */
    public function getQuestionsForUser(Test $test): array
    {
        // generate question IDs using existing generator
        $questionIds = app(QuestionGeneratorService::class)
            ->generate($test);

        // fetch questions with options
        $questions = Question::with([
            'options:id,question_id,option_text,sequence'
        ])
            ->whereIn('id', $questionIds)
            ->orderByRaw("FIELD(id," . implode(',', $questionIds) . ")")
            ->get();

        return $questions->map(function ($q) {
            return [
                'id' => $q->id,
                'section_id' => $q->section_id,
                'question' => $q->question_text,
                'type' => $q->question_type,
                'difficulty' => $q->difficulty,
                'options' => $q->options
                    ->sortBy('sequence')
                    ->values()
                    ->map(fn ($opt) => [
                        'id' => $opt->id,
                        'text' => $opt->option_text,
                        'sequence' => $opt->sequence,
                    ]),
            ];
        })->values()->toArray();
    }
}

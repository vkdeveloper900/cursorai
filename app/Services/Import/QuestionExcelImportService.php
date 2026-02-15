<?php

namespace App\Services\Import;

use App\Models\Section;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Support\Facades\DB;

class QuestionExcelImportService
{
    public function import(array $rows): array
    {
        $inserted = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {

                if (
                    empty($row['section']) ||
                    empty($row['question_type']) ||
                    empty($row['question'])
                ) {
                    $errors[] = "Row ".($index + 2).": Missing required fields";
                    continue;
                }

                $section = Section::firstOrCreate([
                    'name' => trim($row['section'])
                ]);

                $question = Question::create([
                    'section_id' => $section->id,
                    'question_text' => $row['question'],
                    'question_type' => strtolower($row['question_type']),
                    'difficulty' => strtolower($row['difficulty'] ?? 'easy'),
                    'status' => 1,
                ]);

                // MCQ
                if ($row['question_type'] === 'mcq') {

                    $options = [
                        'A' => $row['option_a'],
                        'B' => $row['option_b'],
                        'C' => $row['option_c'],
                        'D' => $row['option_d'],
                    ];

                    foreach ($options as $key => $text) {
                        if (!$text) continue;

                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $text,
                            'is_correct' => strtoupper($row['correct_option']) === $key,
                            'sequence' => ord($key) - 64,
                        ]);
                    }
                }

                // SCALE
                if ($row['question_type'] === 'scale') {

                    for ($i = 1; $i <= 5; $i++) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $row["scale_{$i}_text"],
                            'score_value' => $row["scale_{$i}_score"],
                            'sequence' => $i,
                        ]);
                    }
                }

                $inserted++;
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'inserted' => $inserted,
            'errors' => $errors,
        ];
    }

}

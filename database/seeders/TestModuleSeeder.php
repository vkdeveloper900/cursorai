<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Section;
use App\Models\Test;
use App\Models\TestSection;
use App\Models\TestSectionRule;
use Illuminate\Database\Seeder;

class TestModuleSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1️⃣ SECTIONS
        |--------------------------------------------------------------------------
        */
        $sections = [
            'Personality',
            'Multiple Intelligence',
            'Interest Assessment',
            'Aptitude Battery',
            'Emotional Intelligence',
            'Reasoning',
            'Mathematics',
            'English',
            'General Knowledge',
        ];


        $sectionIds = [];

        foreach ($sections as $name) {
            $section = Section::firstOrCreate(['name' => $name]);
            $sectionIds[$name] = $section->id;
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ QUESTIONS (5 PER DIFFICULTY PER SECTION)
        |--------------------------------------------------------------------------
        */
        $difficulties = ['easy', 'medium', 'hard'];

        foreach ($sectionIds as $sectionName => $sectionId) {

            foreach ($difficulties as $difficulty) {

                /*
                |----------------------------------
                | 5 QUESTIONS
                |----------------------------------
                | Use updateOrCreate with question_text + section_id as unique keys
                |----------------------------------
                */
                for ($i = 1; $i <= 5; $i++) {

                    $questionText = "{$sectionName} {$difficulty} Question {$i}";
                    
                    // MCQ for Reasoning & Mathematics
                    if (in_array($sectionName, ['Reasoning', 'Mathematics'])) {

                        $question = Question::updateOrCreate(
                            [
                                'section_id' => $sectionId,
                                'question_text' => "{$sectionName} {$difficulty} MCQ Question {$i}",
                            ],
                            [
                                'question_type' => 'mcq',
                                'difficulty' => $difficulty,
                                'status' => 1,
                            ]
                        );

                        $correctIndex = 0; // Fixed for consistent seeding
                        $options = ['Option A', 'Option B', 'Option C', 'Option D'];

                        foreach ($options as $index => $text) {
                            QuestionOption::updateOrCreate(
                                [
                                    'question_id' => $question->id,
                                    'option_text' => $text,
                                ],
                                [
                                    'is_correct' => $index === $correctIndex,
                                    'sequence' => $index + 1,
                                ]
                            );
                        }

                    } // SCALE for English & GK
                    else {

                        $question = Question::updateOrCreate(
                            [
                                'section_id' => $sectionId,
                                'question_text' => "{$sectionName} {$difficulty} Scale Question {$i}",
                            ],
                            [
                                'question_type' => 'scale',
                                'difficulty' => $difficulty,
                                'status' => 1,
                            ]
                        );

                        $scaleOptions = [
                            ['Strongly Disagree', 1],
                            ['Disagree', 2],
                            ['Neutral', 3],
                            ['Agree', 4],
                            ['Strongly Agree', 5],
                        ];

                        foreach ($scaleOptions as $index => $opt) {
                            QuestionOption::updateOrCreate(
                                [
                                    'question_id' => $question->id,
                                    'option_text' => $opt[0],
                                ],
                                [
                                    'score_value' => $opt[1],
                                    'sequence' => $index + 1,
                                ]
                            );
                        }
                    }
                }

                /*
                |----------------------------------
                | ➕ EXTRA 1 SCALE QUESTION (ALL SECTIONS)
                |----------------------------------
                */
                $extraScale = Question::updateOrCreate(
                    [
                        'section_id' => $sectionId,
                        'question_text' => "{$sectionName} {$difficulty} Extra Scale Question",
                    ],
                    [
                        'question_type' => 'scale',
                        'difficulty' => $difficulty,
                        'status' => 1,
                    ]
                );

                $extraScaleOptions = [
                    ['Strongly Disagree', 1],
                    ['Disagree', 2],
                    ['Neutral', 3],
                    ['Agree', 4],
                    ['Strongly Agree', 5],
                ];

                foreach ($extraScaleOptions as $index => $opt) {
                    QuestionOption::updateOrCreate(
                        [
                            'question_id' => $extraScale->id,
                            'option_text' => $opt[0],
                        ],
                        [
                            'score_value' => $opt[1],
                            'sequence' => $index + 1,
                        ]
                    );
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | 3️⃣ MASTER TEST
        |--------------------------------------------------------------------------
        */
        $test = Test::updateOrCreate(
            ['title' => 'Career Aptitude Full Demo Test'],
            [
                'intro' => 'All sections with balanced difficulty',
                'instructions' => 'No negative marking. All questions compulsory.',
                'difficulty' => 'mixed',
                'total_time' => 60,
                'status' => 'draft',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ TEST SECTIONS + RULES
        |--------------------------------------------------------------------------
        */
        $sequence = 1;

        foreach ($sectionIds as $sectionId) {

            $testSection = TestSection::updateOrCreate(
                [
                    'test_id' => $test->id,
                    'section_id' => $sectionId,
                ],
                [
                    'total_questions' => 5,
                    'marks_per_question' => 1,
                    'section_time' => 15,
                    'sequence' => $sequence++,
                ]
            );

            $rules = [
                [
                    'difficulty' => 'easy',
                    'question_count' => 2,
                ],
                [
                    'difficulty' => 'medium',
                    'question_count' => 2,
                ],
                [
                    'difficulty' => 'hard',
                    'question_count' => 1,
                ],
            ];

            foreach ($rules as $rule) {
                TestSectionRule::updateOrCreate(
                    [
                        'test_section_id' => $testSection->id,
                        'difficulty' => $rule['difficulty'],
                    ],
                    [
                        'question_count' => $rule['question_count'],
                    ]
                );
            }
        }
    }
}

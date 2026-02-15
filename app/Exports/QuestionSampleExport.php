<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class QuestionSampleExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'section',
            'difficulty',
            'question_type',
            'question',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'correct_option',
            'scale_1_text', 'scale_1_score',
            'scale_2_text', 'scale_2_score',
            'scale_3_text', 'scale_3_score',
            'scale_4_text', 'scale_4_score',
            'scale_5_text', 'scale_5_score',
        ];
    }

    public function array(): array
    {
        return [
            [
                'Reasoning',
                'easy',
                'mcq',
                'If all cats are animals and some animals are wild, which is correct?',
                'All cats are wild',
                'Some cats may be wild',
                'No cats are wild',
                'All animals are cats',
                'B',
                '', '', '', '', '', '', '', '', '', ''
            ],
            [
                'Personality Assessment',
                'medium',
                'scale',
                'I feel confident while speaking in English.',
                '', '', '', '', '',
                'Strongly Disagree', 1,
                'Disagree', 2,
                'Neutral', 3,
                'Agree', 4,
                'Strongly Agree', 5,
            ],
        ];
    }
}

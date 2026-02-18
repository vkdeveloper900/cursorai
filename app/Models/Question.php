<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'section_id',
        'question_text',
        'difficulty',
        'explanation',
    ];

    public function section()
    {
        return $this->belongsTo(QuestionSection::class, 'section_id');
    }
}

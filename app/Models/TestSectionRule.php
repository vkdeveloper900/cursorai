<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestSectionRule extends Model
{
    protected $fillable = [
        'test_section_id',
        'difficulty',
        'question_count'
    ];
}


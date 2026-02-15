<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestSection extends Model
{
    protected $fillable = [
        'test_id',
        'section_id',
        'total_questions',
        'section_time',
        'marks_per_question',
        'sequence'
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function rules()
    {
        return $this->hasMany(TestSectionRule::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionSection extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class, 'section_id');
    }
}

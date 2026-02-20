<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}


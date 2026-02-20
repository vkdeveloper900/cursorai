<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'title',
        'intro',
        'instructions',
        'difficulty',
        'total_time',
        'configuration',
        'status'
    ];

    protected $casts = [
        'configuration' => 'array',
    ];

    public function testSections()
    {
        return $this->hasMany(TestSection::class);
    }
}


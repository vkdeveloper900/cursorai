<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    protected $fillable = [
        'name',
        'description',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
    ];
}

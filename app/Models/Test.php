<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = [
        'name',
        'description',
        'total_time',
        'rule_id',
    ];

    public function rule()
    {
        return $this->belongsTo(Rule::class);
    }

}

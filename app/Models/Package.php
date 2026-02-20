<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'name','slug','description','price',
        'validity_days','attempt_limit','status'
    ];

    public function tests()
    {
        return $this->belongsToMany(Test::class, 'package_tests');
    }


}


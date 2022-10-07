<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'imagepath_1',
        'imagepath_2',
        'imagepath_3',
        'imagepath_4',
        'imagepath_5',
    ];
    public function user_subjects()
  {
    return $this->hasMany(UserSubject::class);
  }
}

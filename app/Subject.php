<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'imagepath',
    ];
    public function user_subjects()
  {
    return $this->hasMany(UserSubject::class);
  }
}

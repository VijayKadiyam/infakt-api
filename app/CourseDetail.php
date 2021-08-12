<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CourseDetail extends Model
{
    protected $fillable = [
        'title', 'description', 'no_of_hrs', 'imagepath', 'videolink'
      ];
    
      public function course()
      {
        return $this->belongsTo(Course::class);
      }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'course_name', 'description', 'instructor', 'no_of_hrs', 'imagepath'
      ];
    
      public function site()
      {
        return $this->belongsTo(Site::class);
      }
    
      public function course_details()
      {
        return $this->hasMany(CourseDetail::class);
      }
}

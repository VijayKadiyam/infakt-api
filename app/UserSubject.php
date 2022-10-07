<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSubject extends Model
{
    protected $fillable = [
        'user_id',
        'subject_id',
     ];

    public function company()
  {
    return $this->belongsTo(Company::class);
  }

  public function user()
   {
      return $this->belongsTo(User::class);
   }

   public function subject()
   {
       return $this->belongsTo(Subject::class);
   }

}

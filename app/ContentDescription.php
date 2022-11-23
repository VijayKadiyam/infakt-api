<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentDescription extends Model
{
   protected $fillable = [
      'content_id',
      'level',
      'title',
      'description',
   ];

   public function content()
   {
      return $this->belongsTo(Content::class)->where('is_active', true);
   }
   public function assignments()
   {
      return $this->hasMany(Assignment::class)
         ->where('is_deleted', false)
         ->where('is_active', true);
   }
}

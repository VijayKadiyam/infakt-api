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
}

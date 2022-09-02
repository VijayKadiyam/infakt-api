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
}

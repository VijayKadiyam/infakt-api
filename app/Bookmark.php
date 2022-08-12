<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
  protected $fillable = [
    'user_id',
    'content_id',
    'is_deleted',
  ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
   protected $fillable = [
    'user_id',
    'collection_name',
       'is_deleted',
    ];

   
}

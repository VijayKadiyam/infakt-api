<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
   protected $fillable = [
    'tagline',
    'info',
    'info_1',
    'description',
   ];

   protected $table = 'about_uses';
}

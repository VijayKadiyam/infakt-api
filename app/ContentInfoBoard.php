<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentInfoBoard extends Model
{
    protected $fillable = [
        'content_id',
        'board_id',
    ];
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'content_name',
        'content_type',
        'written_by_id',
        'reading_time',
        'content_metadata',
        'easy_content',
        'med_content',
        'hard_content',
    ];
}

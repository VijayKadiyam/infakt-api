<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EtArticle extends Model
{
    protected $fillable = [
        'et_xml_id',
        'edition_name',
        'story_id',
        'story_date',
        'headline',
        'byline',
        'category',
        'drophead',
        'content',
        'word_count',
    ];
}

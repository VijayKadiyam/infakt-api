<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ToiArticle extends Model
{
    protected $fillable = [
        'toi_xml_id',
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

    public function contents()
    {
        return $this->hasMany(Content::class);
    }
}

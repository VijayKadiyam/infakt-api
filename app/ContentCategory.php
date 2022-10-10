<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentCategory extends Model
{
    protected $fillable = [
        'content_id',
        'category_id',
    ];
    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

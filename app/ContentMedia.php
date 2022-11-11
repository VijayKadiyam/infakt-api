<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentMedia extends Model
{
    protected $fillable = [
        'content_id',
        'mediapath',
    ];
    public function content()
    {
        return $this->belongsTo(Content::class)->where('is_active', true);
    }
}

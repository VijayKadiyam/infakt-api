<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentSubject extends Model
{
    protected $fillable = [
        'content_id',
        'subject_id',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}

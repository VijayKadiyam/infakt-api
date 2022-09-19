<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentGrade extends Model
{
    protected $fillable = [
        'content_id',
        'grade_id',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}

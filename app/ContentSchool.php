<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentSchool extends Model
{
    protected $fillable = [
        'content_id',
        'company_id',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentHiddenClasscode extends Model
{
    protected $fillable = [
        'content_id',
        'classcode_id',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}

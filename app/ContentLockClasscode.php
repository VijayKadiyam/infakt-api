<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentLockClasscode extends Model
{
    protected $fillable = [
        'content_id',
        'classcode_id',
        'created_by_id',
        'level',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function classcode()
    {
        return $this->belongsTo(Classcode::class);
    }
}

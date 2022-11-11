<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentHiddenClasscode extends Model
{
    protected $fillable = [
        'content_id',
        'classcode_id',
        'created_by_id',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class)->where('is_active', true);
    }
}

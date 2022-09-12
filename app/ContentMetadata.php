<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentMetadata extends Model
{
    protected $fillable = [
        'content_id',
        'metadata_type',
        'color_class',
        'selected_text',
        'annotation',
        'user_id',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function content_metadata_classcodes()
    {
        return $this->hasMany(ContentMetadataClasscode::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentMetadataClasscode extends Model
{
    protected $fillable = [
        'content_metadata_id',
        'classcode_id',
    ];

    public function content_metadata()
    {
        return $this->belongsTo(ContentMetadata::class);
    }
}

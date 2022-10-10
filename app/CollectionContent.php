<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CollectionContent extends Model
{
    protected $fillable = [
        'collection_id',
        'content_id',
        'is_deleted',
    ];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
    public function content()
    {
        return $this->belongsTo(Content::class)
            ->with('content_subjects', 'content_medias', 'content_metadatas', 'content_descriptions', 'content_hidden_classcodes', 'content_lock_classcodes', 'content_reads', 'assignments');
    }
}

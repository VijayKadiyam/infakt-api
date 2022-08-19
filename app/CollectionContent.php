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
}

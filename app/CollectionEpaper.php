<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CollectionEpaper extends Model
{
    protected $fillable = [
        'epaper_collection_id',
        'toi_article_id',
        'et_article_id',
        'is_deleted',
    ];

    public function epaper_collection()
    {
        return $this->belongsTo(EpaperCollection::class);
    }
    public function toi_article()
    {
        return $this->belongsTo(ToiArticle::class);
    }
    public function et_article()
    {
        return $this->belongsTo(EtArticle::class);
    }
}

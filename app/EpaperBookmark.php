<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EpaperBookmark extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'toi_article_id',
        'et_article_id',
        'is_deleted',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
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

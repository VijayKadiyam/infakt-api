<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'is_deleted',
    ];
    public function contents()
    {
        return $this->belongsToMany(Content::class, 'content_categories', 'category_id', 'content_id')->where('is_active', true);
    }
}

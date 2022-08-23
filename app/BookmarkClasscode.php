<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookmarkClasscode extends Model
{
    protected $fillable = [
        'bookmark_id',
        'classcode_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function bookmark()
    {
        return $this->belongsTo(Bookmark::class);
    }
    public function classcode()
    {
        return $this->belongsTo(Classcode::class);
    }
}

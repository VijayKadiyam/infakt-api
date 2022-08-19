<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentRead extends Model
{
    protected $fillable = [
        'company_id',
        'content_id',
        'user_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function contents()
    {
        return $this->belongsTo(Content::class)->with('content_subjects', 'content_medias');
    }

    public function users()
    {
        return $this->belongsTo(User::class)->with('roles');
    }
}

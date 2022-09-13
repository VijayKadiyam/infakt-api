<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentAssignToRead extends Model
{
    protected $fillable = [

        'company_id',
        'content_id',
        'collection_id',
        'classcode_id',
        'created_by_id',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function collecton()
    {
        return $this->belongsTo(Collection::class);
    }
    public function classcode()
    {
        return $this->belongsTo(Classcode::class);
    }
    public function created_by()
    {
        return $this->belongsTo(User::class);
    }
}

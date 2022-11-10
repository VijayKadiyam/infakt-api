<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EpaperCollection extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'collection_name',
        'is_deleted',
    ];


    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

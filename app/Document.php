<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'company_id',
        'image_path',
        'description',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

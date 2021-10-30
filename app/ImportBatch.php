<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportBatch extends Model
{
    protected $fillable = [
        'type',
        'batch_no'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

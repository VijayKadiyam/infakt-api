<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CollectionClasscode extends Model
{
    protected $fillable = [
        'collection_id',
        'classcode_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
    public function classcode()
    {
        return $this->belongsTo(Classcode::class);
    }
}

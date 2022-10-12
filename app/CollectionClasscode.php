<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CollectionClasscode extends Model
{
    protected $fillable = [
        'company_id',
        'collection_id',
        'classcode_id',
        'shared_by_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function collection()
    {
        return $this->belongsTo(Collection::class)->with('user', 'collection_contents');
    }
    public function classcode()
    {
        return $this->belongsTo(Classcode::class);
    }
    public function shared_by()
    {
        return $this->belongsTo(User::class);
    }
}

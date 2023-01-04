<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'standard_id',
        'board_id',
        'is_active',
        'is_deleted',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }

    public function classcodes()
    {
        return $this->hasMany(Classcode::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserClasscode extends Model
{
    protected $fillable = [
        'user_id',
        'classcode_id',
        'start_date',
        'end_date',
        'is_active',
        'is_deleted',
        'standard_id',
        'section_id',
    ];
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}

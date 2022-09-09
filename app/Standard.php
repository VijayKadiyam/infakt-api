<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Standard extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'is_deleted',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class)->with('classcodes');
    }

    public function classcodes()
    {
        return $this->hasMany(Classcode::class);
    }
    public function user_classcoedes()
    {
        return $this->hasMany(UserClasscode::class)->with('user');
    }
}

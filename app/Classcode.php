<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classcode extends Model
{
    protected $fillable = [
        'company_id',
        'standard_id',
        'section_id',
        'subject_name',
        'classcode',
        'is_deleted',
        'is_active',
        'is_optional',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class)->with('standard');
    }
    public function assignment_classcodes()
    {
        return $this->hasMany(AssignmentClasscode::class);
    }
    public function content_lock_classcodes()
    {
        return $this->hasMany(ContentLockClasscode::class);
    }
}

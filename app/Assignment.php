<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'company_id',
        'assignment_type',
        'created_by_id',
        'student_instructions',
        'content_id',
        'duration',
        'documentpath',
        'maximum_marks',
        'is_deleted',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assignmnet()
    {
        return $this->belongsTo(Assignment::class);
    }
}

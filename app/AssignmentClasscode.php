<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentClasscode extends Model
{
    protected $fillable = [
        'assignment_id',
        'classcode_id',
        'start_date',
        'end_date',
        'is_deleted',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assignment_classcodes()
    {
        return $this->hasMany(AssignmentClasscode::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentClasscode extends Model
{
    protected $fillable = [
        'company_id',
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

    public function assignment()
    {
        return $this->belongsTo(Assignment::class)->with('user_assignments');
    }

    public function classcode()
    {
        return $this->belongsTo(Classcode::class);
    }
}

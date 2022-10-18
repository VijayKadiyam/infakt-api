<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAssignmentTiming extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'assignment_id',
        'user_assignment_id',
        'timestamp',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
    public function user_assignment()
    {
        return $this->belongsTo(UserAssignment::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAssignment extends Model
{
    protected $fillable = [
        "user_id",
        "assignment_id",
        "submission_date",
        "score",
        "documentpath",
        'is_deleted',
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
}

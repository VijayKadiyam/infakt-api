<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAssignmentSelectedAnswer extends Model
{
    protected $fillable = [
        "user_id",
        "assignment_id",
        "assignment_question_id",
        "selected_option_sr_no",
        "is_correct",
        "marks_obtained",
        "documentpath",
        "description",
        "is_deleted",
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

    public function assignment_question()
    {
        return $this->belongsTo(AssignmentQuestion::class);
    }
}
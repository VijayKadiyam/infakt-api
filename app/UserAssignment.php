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
        'time_taken', 'total_questions', 'answered_questions', 'right_questions', 'wrong_questions', 'result_declared',
        'feedback',
        'start_time',
        'time_data',
    ];
    protected $casts = [
        'time_data'  =>  'array'
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
        return $this->belongsTo(Assignment::class)
            ->where('is_deleted', false);
    }

    public function user_assignment_selected_answers()
    {
        return $this->hasMany(UserAssignmentSelectedAnswer::class);
    }
}

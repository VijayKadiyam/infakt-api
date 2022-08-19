<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentQuestion extends Model
{
    protected $fillable = [
        'assignment_id',
        'description',
        'correct_option_sr_no',
        'marks',
        'negative_marks',
        'is_deleted',
        'sr_no',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function assignment_question_options()
    {
        return $this->hasMany(AssignmentQuestionOption::class);
    }
}

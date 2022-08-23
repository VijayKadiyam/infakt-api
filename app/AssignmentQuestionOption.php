<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentQuestionOption extends Model
{
    protected $fillable = [
        'assignment_question_id',
        'option1',
        'option2',
        'option3',
        'option4',
        'is_deleted',
        'option5',
        'option6',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assignment_question()
    {
        return $this->belongsTo(AssignmentQuestion::class);
    }
}

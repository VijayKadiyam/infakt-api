<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentQuestion extends Model
{
    protected $casts = [
        'correct_options' => 'array'
    ];

    protected $fillable = [
        'assignment_id',
        'description',
        'correct_option_sr_no',
        'marks',
        'negative_marks',
        'is_deleted',
        'sr_no',
        'option1',
        'option2',
        'option3',
        'option4',
        'option5',
        'option6',
        'model_answer',
        'question_type',
        'correct_options',
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
    public function assignment_question_correct_options()
    {
        return $this->hasMany(AssignmentQuestionCorrectOption::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Tests\Feature\AssignmentQuestionTest;

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

    public function assignmnet_classcodes()
    {
        return $this->hasMany(AssignmentClasscode::class);
    }

    public function assignmnet_questions()
    {
        return $this->hasMany(AssignmentQuestion::class);
    }

    public function assignmnet_question_options()
    {
        return $this->hasMany(AssignmentQuestionOption::class);
    }
}

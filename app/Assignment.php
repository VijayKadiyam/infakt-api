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
        'assignment_title',
        'is_draft',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assignment_classcodes()
    {
        return $this->hasMany(AssignmentClasscode::class);
    }

    public function assignment_questions()
    {
        return $this->hasMany(AssignmentQuestion::class)->with('assignment_question_options');
    }

    public function assignment_extensions()
    {
        return $this->hasMany(AssignmentExtension::class);
    }
}

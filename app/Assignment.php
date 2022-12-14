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
        'content_description_id',
        'duration',
        'documentpath',
        'maximum_marks',
        'is_deleted',
        'assignment_title',
        'is_draft',
        'collection_id',
        'start_time',
        'time_data',
        'model_answer',
        'is_active',
        'status',
        'remarks',
        'copied_from_assignment_id',
        'is_modified',
    ];
    protected $casts = [
        'time_data'  =>  'array'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function created_by()
    {
        return $this->belongsTo(User::class)
            ->with('roles');
    }

    public function content()
    {
        return $this->belongsTo(Content::class)->where('is_active', true);
    }

    public function content_description()
    {
        return $this->belongsTo(ContentDescription::class);
    }

    public function assignment_classcodes()
    {
        return $this->hasMany(AssignmentClasscode::class)
            ->with('classcode');
    }

    public function assignment_questions()
    {
        return $this->hasMany(AssignmentQuestion::class)->with('assignment_question_options', 'assignment_question_correct_options');
    }

    public function assignment_extensions()
    {
        return $this->hasMany(AssignmentExtension::class)
            ->with('user');
    }

    public function my_results($userId = '')
    {
        if ($userId == '')
            return $this->hasMany(UserAssignment::class)
                ->with('user_assignment_selected_answers')
                ->where('user_id', '=', request()->user()->id);
        else
            return $this->hasMany(UserAssignment::class)
                ->with('user_assignment_selected_answers')
                ->where('user_id', '=', $userId);
    }

    public function my_assignment_extensions($userId = '')
    {
        if ($userId == '')
            return $this->hasMany(AssignmentExtension::class)
                ->where('user_id', '=', request()->user()->id);
        else
            return $this->hasMany(AssignmentExtension::class)
                ->where('user_id', '=', $userId);
    }

    public function my_assignment_classcodes()
    {
        return $this->hasMany(AssignmentClasscode::class);
    }


    public function user_assignments()
    {
        return $this->hasMany(UserAssignment::class)
            ->with('user');
    }

    public function classcodes()
    {
        return $this->belongsToMany(Classcode::class, 'assignment_classcodes', 'assignment_id', 'classcode_id')->with('students');
    }
}

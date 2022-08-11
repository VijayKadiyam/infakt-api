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
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}

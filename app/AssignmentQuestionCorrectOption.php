<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentQuestionCorrectOption extends Model
{
    protected $fillable = [
        'assignment_question_id',
        'option',
        'is_deleted'
    ];

    public function assignment_question()
    {
        return $this->belongsTo(AssignmentQuestion::class);
    }
}

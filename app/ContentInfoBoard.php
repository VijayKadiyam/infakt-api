<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentInfoBoard extends Model
{
    protected $fillable = [
        'content_id',
        'board_id',
        'learning_outcome',
    ];
    public function content()
    {
        return $this->belongsTo(Content::class)->where('is_active', true);
    }
    public function board()
    {
        return $this->belongsTo(Board::class);
    }
    public function content_info_board_grades()
    {
        return $this->hasMany(ContentInfoBoardGrade::class)->with('grade');
    }
    public function content_info_board_subjects()
    {
        return $this->hasMany(ContentInfoBoardSubject::class)->with('subject');
    }
}

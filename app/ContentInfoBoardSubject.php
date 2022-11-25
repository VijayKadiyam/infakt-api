<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentInfoBoardSubject extends Model
{
    protected $fillable = [
        'content_info_board_id',
        'subject_id',
    ];

    public function content_info_board()
    {
        return $this->belongsTo(ContentInfoBoard::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}

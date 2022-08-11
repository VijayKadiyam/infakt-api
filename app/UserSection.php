<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSection extends Model
{
    protected $fillable = [
        'user_id',
        'section_id',
        'start_date',
        'end_date',
        'is_active',
        'is_deleted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}

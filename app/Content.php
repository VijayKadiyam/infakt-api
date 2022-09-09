<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'content_name',
        'content_type',
        'written_by_name',
        'reading_time',
        'content_metadata',
        'easy_content',
        'med_content',
        'original_content',
        'grade_id',
        'learning_outcome',
        'for_school_type',
        'board_id',
        'specific_to',
        'school_id',
        'info_board_id',
    ];

    // public function written_by()
    // {
    //     return $this->belongsTo(User::class);
    // }

    public function content_subjects()
    {
        return $this->hasMany(ContentSubject::class)->with('subject');
    }
    public function content_medias()
    {
        return $this->hasMany(ContentMedia::class);
    }
    public function content_reads()
    {
        return $this->hasMany(ContentRead::class);
    }
    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }
    public function content_metadatas()
    {
        return $this->hasMany(ContentMetadata::class);
    }
    public function content_descriptions()
    {
        return $this->hasMany(ContentDescription::class);
    }
}

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
        'publication',
        'adapted_by',
        'featured_image_path',
        'created_by_id',
        'is_active',
        'is_draft',
        'is_approved',
        'toi_article_id',
        'et_article_id',
        'remarks'
    ];

    public function created_by()
    {
        return $this->belongsTo(User::class)
            ->with('roles');
    }

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
        return $this->hasMany(ContentDescription::class)
            ->orderBy('level', 'DESC');
    }
    public function content_hidden_classcodes()
    {
        return $this->hasMany(ContentHiddenClasscode::class);
    }
    public function content_lock_classcodes()
    {
        return $this->hasMany(ContentLockClasscode::class);
    }
    public function content_assign_to_reads()
    {
        return $this->hasMany(ContentAssignToRead::class);
    }
    public function content_grades()
    {
        return $this->hasMany(ContentGrade::class)
            ->with('grade');
    }
    public function content_boards()
    {
        return $this->hasMany(ContentBoard::class)
            ->with('board');
    }
    public function content_info_boards()
    {
        return $this->hasMany(ContentInfoBoard::class);
    }
    public function content_schools()
    {
        return $this->hasMany(ContentSchool::class);
    }
    public function collection_contents()
    {
        return $this->hasMany(CollectionContent::class);
    }
    public function content_categories()
    {
        return $this->hasMany(ContentCategory::class)
            ->with('category');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
    public function my_assignments()
    {
        return $this->hasMany(Assignment::class)->where('created_by_id', request()->user()->id);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classcode extends Model
{
    protected $fillable = [
        'company_id',
        'standard_id',
        'section_id',
        'subject_name',
        'classcode',
        'is_deleted',
        'is_active',
        'is_optional',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function standard()
    {
        return $this->belongsTo(Standard::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class)->with('standard');
    }
    public function user_classcodes()
    {
        return $this->hasMany(UserClasscode::class);
    }
    public function assignments()
    {
        return $this->belongsToMany(Assignment::class, 'assignment_classcodes', 'classcode_id', 'assignment_id')
            ->latest()
            ->withTimestamps();
    }
    public function assignment_classcodes()
    {
        return $this->hasMany(AssignmentClasscode::class);
    }
    public function content_lock_classcodes()
    {
        return $this->hasMany(ContentLockClasscode::class);
    }
    public function content_assign_to_reads()
    {
        return $this->hasMany(ContentAssignToRead::class);
    }
    public function students()
    {
        return $this->belongsToMany(User::class, 'user_classcodes', 'classcode_id', 'user_id')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'STUDENT');
            });
    }
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'user_classcodes', 'classcode_id', 'user_id')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'TEACHER');
            });
    }
    public function content_metadatas()
    {
        return $this->belongsToMany(ContentMetadata::class, 'content_metadata_classcodes', 'classcode_id', 'content_metadata_id')
            ->with('user', 'content');
    }
    public function annotations()
    {
        return $this->belongsToMany(ContentMetadata::class, 'content_metadata_classcodes', 'classcode_id', 'content_metadata_id')
            ->where('metadata_type', 'ANNOTATION')
            ->with('user');
    }
    public function highlights()
    {
        return $this->belongsToMany(ContentMetadata::class, 'content_metadata_classcodes', 'classcode_id', 'content_metadata_id')
            ->where('metadata_type', 'HIGHLIGHT');
    }
    public function dictionaries()
    {
        return $this->belongsToMany(ContentMetadata::class, 'content_metadata_classcodes', 'classcode_id', 'content_metadata_id')
            ->where('metadata_type', 'DICTIONARY');
    }
}

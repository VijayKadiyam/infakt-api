<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignmentExtension extends Model
{
    protected $fillable = [
        'assignment_id',
        'user_id',
        'extension_reason',
        'expected_extension_date',
        'approved_extension_date',
        'is_approved',
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

    public function users()
    {
        return $this->hasMany(User::class);
    }
}

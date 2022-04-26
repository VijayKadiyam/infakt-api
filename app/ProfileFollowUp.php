<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfileFollowUp extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'profile_id',
        'remarks',
        'next_meeting_date',
        'is_active',
        'is_deleted',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class)->where(['is_active' => true, 'is_deleted' => false]);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->where(['is_active' => true, 'is_deleted' => false]);
    }
}

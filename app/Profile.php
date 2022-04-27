<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'visit_call',
        'mark_in_lat',
        'mark_in_lng',
        'mark_out_lat',
        'mark_out_lng',
        'date',
        'mobile_1',
        'email_1',
        'photo_1_path',
        'company_name',
        'industry',
        'employee_size',
        'turnover',
        'head_office',
        'address',
        'website',
        'contact_1_mobile',
        'contact_1_email',
        'contact_2_mobile',
        'contact_2_email',
        'contact_1_name',
        'contact_2_name',
        'product_offered',
        'deal_date',
        'agreement_date',
        'terms',
        'remarks',
        'next_meeting_date',
        'is_active',
        'is_deleted',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->where(['active' => true]);
    }

    public function profile_follow_ups()
    {
        return $this->hasMany(ProfileFollowUp::class)->where(['is_active' => true, 'is_deleted' => false]);
    }
}

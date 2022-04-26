<?php

use App\Profile;
use Faker\Generator as Faker;

$factory->define(Profile::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'visit_call' => 'visit_call',
        'mark_in_lat' => 'mark_in_lat',
        'mark_in_lng' => 'mark_in_lng',
        'mark_out_lat' => 'mark_out_lat',
        'mark_out_lng' => 'mark_out_lng',
        'date' => 'date',
        'mobile_1' => 1,
        'email_1' => 'email_1',
        'photo_1_path' => 'photo_1_path',
        'company_name' => 'company_name',
        'industry' => 'industry',
        'employee_size' => 'employee_size',
        'turnover' => 'turnover',
        'head_office' => 'head_office',
        'address' => 'address',
        'website' => 'website',
        'contact_1_mobile' => 1,
        'contact_1_email' => 'contact_1_email',
        'contact_2_mobile' => 1,
        'contact_2_email' => 'contact_2_email',
        'contact_1_name' => 'contact_1_name',
        'contact_2_name' => 'contact_2_name',
        'product_offered' => 'product_offered',
        'deal_date' => 'deal_date',
        'agreement_date' => 'agreement_date',
        'terms' => 'terms',
        'remarks' => 'remarks',
        'next_meeting_date' => 'next_meeting_date',
        'is_active' => true,
        'is_deleted' => false,
    ];
});

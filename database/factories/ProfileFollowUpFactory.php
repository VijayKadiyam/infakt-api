<?php

use App\ProfileFollowUp;
use Faker\Generator as Faker;

$factory->define(ProfileFollowUp::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'profile_id' => 1,
        'remarks' => 'remarks',
        'next_meeting_date' => 'next_meeting_date',
        'is_active' => true,
        'is_deleted' => false,
    ];
});

<?php

use Faker\Generator as Faker;
use App\InquiryFollowup;

$factory->define(InquiryFollowup::class, function (Faker $faker) {
    return [
        'date'  =>  'Date 1'
    ];
});

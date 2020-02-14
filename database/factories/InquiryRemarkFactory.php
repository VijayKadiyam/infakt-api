<?php

use Faker\Generator as Faker;
use App\InquiryRemark;

$factory->define(InquiryRemark::class, function (Faker $faker) {
    return [
        'date'  =>  'Date 1'
    ];
});

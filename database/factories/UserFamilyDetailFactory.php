<?php

use Faker\Generator as Faker;
use  App\UserFamilyDetail;

$factory->define(UserFamilyDetail::class, function (Faker $faker) {
    return [
        'name'  =>  'Name 1',
        'dob'   =>  'DOB 1',
        'gender'=>  'Gender 1',
        'relation'  =>  'Relation 1',
        'occupation'=>  'Occupation 1',
        'contact_number'  =>  'Contact Number 1'
    ];
});

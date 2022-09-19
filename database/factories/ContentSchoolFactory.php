<?php

use App\ContentSchool;
use Faker\Generator as Faker;

$factory->define(ContentSchool::class, function (Faker $faker) {
    return [
        'content_id'=>1,
        'company_id'=>1,
    ];
});

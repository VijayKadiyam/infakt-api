<?php

use App\ContentSubject;
use Faker\Generator as Faker;

$factory->define(ContentSubject::class, function (Faker $faker) {
    return [
        'content_id' => 1,
        'subject_id' => 1,
    ];
});

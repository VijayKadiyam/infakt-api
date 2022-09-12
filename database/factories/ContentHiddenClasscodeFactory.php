<?php

use App\ContentHiddenClasscode;
use Faker\Generator as Faker;

$factory->define(ContentHiddenClasscode::class, function (Faker $faker) {
    return [
        'classcode_id' =>  1,
    ];
});

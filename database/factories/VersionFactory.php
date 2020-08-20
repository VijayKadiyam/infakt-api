<?php

use Faker\Generator as Faker;
use App\Version;

$factory->define(Version::class, function (Faker $faker) {
    return [
        'version' =>  '1.0'
    ];
});

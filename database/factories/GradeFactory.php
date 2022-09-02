<?php

use App\Grade;
use Faker\Generator as Faker;

$factory->define(Grade::class, function (Faker $faker) {
    return [
        'name'=>'name',
        'is_active'=>1,
        'is_deleted'=>0,
    ];
});

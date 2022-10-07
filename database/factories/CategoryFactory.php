<?php

use App\Category;
use Faker\Generator as Faker;

$factory->define(Category::class, function (Faker $faker) {
    return [
        'name'=>'name',
        'is_active'=>1,
        'is_deleted'=>0,
    ];
});

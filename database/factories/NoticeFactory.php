<?php

use Faker\Generator as Faker;
use App\Notice;

$factory->define(Notice::class, function (Faker $faker) {
    return [
        'name'  =>  'Name 1',
        'title' =>  'Title 1',
        'description' =>  'Description 1',
    ];
});

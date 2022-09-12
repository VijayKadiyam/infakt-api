<?php

use App\Search;
use Faker\Generator as Faker;

$factory->define(Search::class, function (Faker $faker) {
    return [
        'user_id'=>1,
        'search_type'=>'search_type',
        'search'=>'search',
    ];
});

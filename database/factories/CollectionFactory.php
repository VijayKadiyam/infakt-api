<?php

use App\Collection;
use Faker\Generator as Faker;

$factory->define(Collection::class, function (Faker $faker) {
    return [
       'company_id'=> 1,
       'user_id'=>1,
       'collection_name'=>'collection_name',
       'is_deleted'=>0,
    ];
});

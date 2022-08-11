<?php

use App\UserStandard;
use Faker\Generator as Faker;

$factory->define(UserStandard::class, function (Faker $faker) {
    return [
        'company_id' => '1',
        'user_id' => '1',
        'standard_id' => '1',
        'start_date' => 'start_date',
        'end_date' => 'end_date',
        'is_active' => '1',
        'is_deleted' => '0',
    ];
});

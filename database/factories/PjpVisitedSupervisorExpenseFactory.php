<?php

use App\PjpVisitedSupervisorExpense;
use Faker\Generator as Faker;

$factory->define(PjpVisitedSupervisorExpense::class, function (Faker $faker) {
    return [
        'company_id' => 2,
        'pjp_visited_supervisor_id' => 1,
        'expense_type' => 'expense_type',
        'travelling_way' => 'travelling_way',
        'transport_mode' => 'transport_mode',
        'km_travelled' => 0,
        'amount' => 0,
        'description' => 'description',
    ];
});

<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'name'              => $faker->name,
        'email'             => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password'          => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token'    => str_random(10),
        'active'            =>  1,
        'phone'             =>  $faker->phoneNumber,
        'doj'               =>  '12-02-2019',
        'dob'               =>  '04-05-1992',
        'company_designation_id'  =>  1,
        'company_state_branch_id' => 1,
        'pf_no'                   =>  '1234567654',
        'uan_no'                  =>  '1234565432',
        'esi_no'                  =>  '234565',
        'salary'                  =>  '5000',
        'employee_code'           =>  '1',
        'asm_area'                =>  'Mumbai',
        'asm_name'                =>  'M'
      ];
});

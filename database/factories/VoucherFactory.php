<?php

use Faker\Generator as Faker;
use App\Voucher;

$factory->define(Voucher::class, function (Faker $faker) {
    return [
      'voucher_type_id' =>  '1',
      'amount'          =>  '200',
      'description'     =>  '100'
    ];
});

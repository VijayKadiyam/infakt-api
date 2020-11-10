<?php

use Illuminate\Database\Seeder;
use App\User;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {

    $user = User::create([
      'name'    =>  'ADMIN',
      'email'   =>  'KVJKUMR@GMAIL.COM', 
      'phone'   =>  '9579862371',
      'active'  =>  1,
      'password'=>  bcrypt('123456'),
    ]);
    $user->assignRole(1);

    $user = User::create([
      'name'    =>  'VIJAYKUMAR',
      'email'   =>  'ADMIN@GMAIL.COM', 
      'phone'   =>  '9579862372',
      'active'  =>  1,
      'password'=>  bcrypt('123456'),
    ]);
    $user->assignRole(2);
    $user->assignCompany(1);
  }
}

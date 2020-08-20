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
      'name'    =>  'Vijaykumar Kadiyam', 
      'email'   =>  'kvjkumr@gmail.com', 
      'phone'   =>  '9579862371',
      'active'  =>  1,
      'password'=>  bcrypt('123456'),
    ]);
    $user->assignRole(1);
  }
}

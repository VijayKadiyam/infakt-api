<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // Role::truncate();
    Role::create(['name' => 'SUPER ADMIN']);
    Role::create(['name' => 'ADMIN']);
    Role::create(['name' => 'EMPLOYEE']);
    Role::create(['name' => 'SUPERVISOR']);
    Role::create(['name' => 'SSM']);
    Role::create(['name' => 'SALES OFFICER']);
    Role::create(['name' => 'AREA HEAD']);
    Role::create(['name' => 'REGIONAL HEAD']);
    Role::create(['name' => 'NATIONAL HEAD']);
    Role::create(['name' => 'DISTRIBUTOR']);
  }
}

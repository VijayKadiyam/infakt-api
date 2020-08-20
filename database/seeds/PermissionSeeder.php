<?php

use Illuminate\Database\Seeder;
use App\Permission;

class PermissionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Permission::truncate();
    Permission::create(['name' => 'Settings']); // 1
    Permission::create(['name' => 'Manage Permissions']); // 2
    Permission::create(['name' => 'Manage Holidays']); // 3
    Permission::create(['name' => 'Manage Profile']); // 4
    Permission::create(['name' => 'Manage Organizations']); // 5
    Permission::create(['name' => 'Manage Designations']); // 6
    Permission::create(['name' => 'Manage States']); // 7
    Permission::create(['name' => 'Manage State Holidays']); // 8
    Permission::create(['name' => 'Manage Users']); // 9
    Permission::create(['name' => 'Manage Supervisors']); // 10
    Permission::create(['name' => 'Manage Leaves']); // 11
    Permission::create(['name' => 'Manage Leave Applications']); // 12
    Permission::create(['name' => 'Sales']); // 13
    Permission::create(['name' => 'Reset Password']); // 14
    Permission::create(['name' => 'Break Types']); // 15
    Permission::create(['name' => 'User Logins']); // 16
    Permission::create(['name' => 'Leave Types']); // 17
    Permission::create(['name' => 'Transport Modes']); // 18
    Permission::create(['name' => 'Travelling Ways']); // 19
    Permission::create(['name' => 'Allowance Types']); // 20
    Permission::create(['name' => 'Voucher Types']); // 21
    Permission::create(['name' => 'Application Approvals']); // 22
    Permission::create(['name' => 'Leave Report']); // 23
    Permission::create(['name' => 'Plan Report']); // 24
    Permission::create(['name' => 'Sales Report']); // 25
    Permission::create(['name' => 'Users Report']); // 26
  }
}

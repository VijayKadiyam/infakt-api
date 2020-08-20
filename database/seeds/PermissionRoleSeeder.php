<?php

use Illuminate\Database\Seeder;
use App\Role;

class PermissionRoleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // Super Admin Permissions
    $role = Role::find(1);
    $role->assignpermission(1); // Settings
    $role->assignpermission(2); // Manage Permissions
    $role->assignpermission(3); // Manage Holidays
    $role->assignpermission(4); // Manage Profile
    $role->assignpermission(5); // Manage Organizations
    $role->assignpermission(6); // Manage Designations
    $role->assignpermission(7); // Manage States
    $role->assignpermission(8); // Manage State Holidays
    $role->assignpermission(9); // Manage Users
    $role->assignpermission(10); // Manage Supervisors
    $role->assignpermission(11); // Manage Leaves
    $role->assignpermission(12); // Manage Leave Applications
    $role->assignpermission(13); // Sales
    $role->assignpermission(14); // Reset Password
    $role->assignpermission(15); // Break Types
    $role->assignpermission(16); // User Logins
    $role->assignpermission(17); // Leave Types
    $role->assignpermission(18); // Transport Modes
    $role->assignpermission(19); // Travelling Ways
    $role->assignpermission(20); // Allowance Types
    $role->assignpermission(21); // Voucher Types
    $role->assignpermission(22); // Application Approvals
    $role->assignpermission(23); // Leave Report
    $role->assignpermission(24); // Plan Report
    $role->assignpermission(25); // Sales Report
    $role->assignpermission(26); // Users Report

    // Admin Permissions
    $role = Role::find(2);
    // $role->assignpermission(1); // Settings
    $role->assignpermission(2); // Manage Permissions
    // $role->assignpermission(3); // Manage Holidays
    $role->assignpermission(4); // Manage Profile
    // $role->assignpermission(5); // Manage Organizations
    $role->assignpermission(6); // Manage Designations
    $role->assignpermission(7); // Manage States
    $role->assignpermission(8); // Manage State Holidays
    $role->assignpermission(9); // Manage Users
    $role->assignpermission(10); // Manage Supervisors
    $role->assignpermission(11); // Manage Leaves
    $role->assignpermission(12); // Manage Leave Applications
    $role->assignpermission(13); // Sales
    $role->assignpermission(14); // Reset Password
    $role->assignpermission(15); // Break Types
    $role->assignpermission(16); // User Logins
  }
}

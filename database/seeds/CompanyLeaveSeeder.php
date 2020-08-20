<?php

use Illuminate\Database\Seeder;
use App\Company;

class CompanyLeaveSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $company = Company::find(1);
    $company->saveDefaultCompanyLeaves();
  }
}

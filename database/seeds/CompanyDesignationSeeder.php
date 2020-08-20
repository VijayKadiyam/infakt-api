<?php

use Illuminate\Database\Seeder;
use App\Company;
use App\CompanyDesignation;

class CompanyDesignationSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $company = Company::find(1);
    $company->saveDefaultDesignations();
  }
}

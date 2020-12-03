<?php

use Illuminate\Database\Seeder;
use App\Company;
use App\Product;

class CompanySeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $company = Company::create([
      'name'    => 'Pousse Management Services Pvt. Ltd.', 
      'email'   =>  'kirit.sayani@pousse.in', 
      'address' =>  '306, Corporate Centre, Nirmal Lifestyle, LBS Road, Mulund West, Mumbai, Maharashtra 400080',
      'phone'   =>  '9820704909'
    ]);

    Product::create([
      'company_id'  =>  $company->id,
      'name'        =>  'Product 1'
    ]);
  }
}

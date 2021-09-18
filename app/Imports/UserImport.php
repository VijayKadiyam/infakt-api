<?php

namespace App\Imports;

use App\CrudeUser;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class UserImport implements ToModel, WithHeadingRow
{
  public function model(array $row)
  {
    $data = [
      // 'crude_users column name' = $row['Excel column name']
      'company_id'  =>  request()->company->id,
      'empid' =>  $row['EMP ID'],
      'region'      =>  $row['Region'],
      'channel'     =>  $row['Channel'],
      'chain_name'        =>  $row['Chain Name'],
      'billing_code'       =>  $row['Billing Code '],
      'store_code'       =>  $row['Store Code'],
      'store_name'        =>  $row['Store Name'],
      'store_address'       =>  $row['Store Address '],
      'ba_name'       =>  $row['BA Name'],
      'location'        =>  $row['Location'],
      'city'       =>  $row['City'],
      'state'       =>  $row['State'],
      'rsm'        =>  $row['RSM'],
      'asm'       =>  $row['ASM'],
      'supervisor_name'       =>  $row['Supervisor Name'],
      'store_type'        =>  $row['Store Type'],
      'brand'       =>  $row['BRAND'],
    ];

    return new CrudeUser($data);
  }

  public function headingRow(): int
  {
    return 1;
  }

  public function batchSize(): int
  {
    return 1000;
  }
}

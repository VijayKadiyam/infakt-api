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
    if ($row['New Store CD'] != '') {
      $data = [
        // 'crude_users column name' = $row['Excel column name']
        'company_id'  =>  request()->company->id,
        'empid' =>  $row['EMP ID'],
        'region'      =>  $row['Region'],
        'channel'     =>  $row['Channel'],
        'chain_name'        =>  $row['Chain Name'],
        'store_code'       =>  $row['New Store CD'],
        'store_name'        =>  $row['Store Name'],
        'ba_name'       =>  $row['BA Name'],
        'location'        =>  $row['Location'],
        'city'       =>  $row['City'],
        'state'       =>  $row['State'],
        'rsm'        =>  $row['RSM'],
        'asm'       =>  $row['ASM'],
        'supervisor_name'       =>  $row['Supervisor Name'],
        'brand'       =>  $row['BRAND'],
        'ba_status'       =>  $row['BA status'],

        //Optional fields 
        'billing_code'       => (array_key_exists('Billing Code', $row) && $row['Billing Code']) ? $row['Billing Code'] : null,
        'store_type'       => (array_key_exists('Store Type', $row) && $row['Store Type']) ? $row['Store Type'] : null,
      ];

      return new CrudeUser($data);
    }
  }

  public function headingRow(): int
  {
    return 2;
  }

  public function batchSize(): int
  {
    return 1000;
  }
}

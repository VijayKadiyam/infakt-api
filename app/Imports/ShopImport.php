<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use App\CrudeShop;

HeadingRowFormatter::default('none');

class ShopImport implements ToModel, WithHeadingRow
{
  public function model(array $row)
  {
    $data = [
      'company_id'          =>  request()->company->id,
      'shop_name'           =>  $row['ShopName'],
      'address'             =>  $row['Address'],
      'contact_person'      =>  $row['ContactPerson'],
      'email'               =>  $row['Email'],
      'shop_type'           =>  $row['ShopType'],
      'beat'                =>  $row['Beat'],
      'week_number'         =>  $row['WeekNumber'],
      'outlet_wisdom_code'  =>  $row['OutletWisdomCode'],
    ];

    return new CrudeShop($data);
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

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
      'company_id'  =>  request()->company->id,
      'empid'       =>  $row['EmpID'],
      'name'        =>  $row['FName'],
      'email'       =>  $row['EMail'],
      'phone'       =>  $row['Mobile'],
    ];

    return new CrudeUser($data);
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

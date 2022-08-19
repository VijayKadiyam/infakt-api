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
      'role_id'  =>  request()->role_id,
      'id_given_by_school' =>  $row['ID'],
      'first_name' =>  $row['First Name'],
      'last_name' =>  $row['Last Name'],
      'email' =>  $row['Email'],
      'contact_number' =>  $row['Contact Number'],
      'gender' =>  $row['Gender'],
      'active' =>  $row['is Active'],
      'joining_date' =>  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['Joining Date'])->format('Y-m-d'),

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

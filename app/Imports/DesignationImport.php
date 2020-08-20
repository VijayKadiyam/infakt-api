<?php

namespace App\Imports;

use App\CrudeDesignation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class DesignationImport implements ToModel, WithHeadingRow
{
  public function model(array $row)
  {
    $data = [
      'company_id'  =>  request()->company->id,
      'name'       =>  $row['Name'],
    ];

    return new CrudeDesignation($data);
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

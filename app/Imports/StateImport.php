<?php

namespace App\Imports;

use App\CrudeState;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class StateImport implements ToModel, WithHeadingRow
{
  public function model(array $row)
  {
    $data = [
      'company_id'  =>  request()->company->id,
      'state'       =>  $row['State'],
      'branch'      =>  $row['Branch'],
    ];

    return new CrudeState($data);
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

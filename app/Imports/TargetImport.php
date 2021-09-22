<?php

namespace App\Imports;
use App\CrudeTarget;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class TargetImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {

      $data = [
        'company_id'  =>  request()->company->id,
        'store_code' =>  $row['Store Code'],
        'target'      =>  $row['Target'],
        'month'  =>  request()->month,
        'year'  =>  request()->year,
      ];
  
      return new CrudeTarget($data);
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

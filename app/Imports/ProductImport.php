<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use App\CrudeProduct;

HeadingRowFormatter::default('none');

class ProductImport implements ToModel, WithHeadingRow
{
  public function model(array $row)
  {
    $data = [
      'company_id'    =>  request()->company->id,
      'product_name'  =>  $row['Product Name'],
      'sku_name'      =>  $row['SKU Name'],
      'invoice_no'    =>  $row['Invoice No'],
      'qty'           =>  $row['Quantity'],
      'unit'          =>  $row['Unit'],
      'price'         =>  $row['Price/Quantity'],
    ];

    return new CrudeProduct($data);
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

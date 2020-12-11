<?php

namespace App\Imports;

use App\CrudeSku;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class SkuImport implements ToModel, WithHeadingRow
{
  public function model(array $row)
  {
    $UNIX_DATE = ($row['DATE'] - 25569) * 86400;
    $date = gmdate("d/m/Y", $UNIX_DATE);

    $data = [
      'company_id'      =>  request()->company->id,
      'distributor_name'=>  $row['DISTRIBUTOR NAME'],
      'sku_name'        =>  $row['SKU NAME'],
      'invoice_no'      =>  $row['INVOICE NO'],
      'date'            =>  $date,
      'qty'             =>  $row['QTY'],
      'unit'            =>  $row['UNIT'],
      'price_per_unit'  =>  $row['PRICE/UNIT'],
      'total_price'     =>  $row['TOTAL PRICE'],
      'sku_type'        =>  $row['SKU TYPE'],
      'offer'           =>  $row['OFFER'],
      'offer_type'      =>  $row['OFFER TYPE'],
    ];

    return new CrudeSku($data);
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

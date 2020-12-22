<?php

namespace App\Imports;

use App\CrudeSale;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class SaleImport implements ToModel, WithHeadingRow
{
  public function model(array $row)
  {
    $data = [
      'company_id'      =>  request()->company->id,
      'invoice_no'      =>  $row['INVOICE NO'],
      'outlet_name'     =>  $row['OUTLET NAME'],
      'uid'             =>  $row['UID'],
      'name_of_person'  =>  $row['NAME OF PERSON'],
      'cell_no'         =>  $row['CELL NO'],
      'sku'             =>  $row['SKU'],
      'qty'             =>  $row['QTY'],
      'unit_price'      =>  $row['PRICE/UNIT'],
      'bill_value'      =>  $row['BILL VALUE'],
      'sku_type'        =>  $row['SKU TYPE'],
      'offer'           =>  $row['OFFER'],
      'offer_type'      =>  $row['OFFER TYPE'],
      'offer_amount'    =>  $row['OFFER AMOUNT'],
      'total_bill_value'=>  $row['TOTAL BILL VALUE'],
      'qty_returned'    =>  $row['QTY RETURNED'],
      'final_bill_value'=>  $row['FINAL BILL VALUE'],
    ];

    return new CrudeSale($data);
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

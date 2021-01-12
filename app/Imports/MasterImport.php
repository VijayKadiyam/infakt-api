<?php

namespace App\Imports;

use App\CrudeMaster;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class MasterImport implements ToModel, WithHeadingRow
{
  public function model(array $row)
  {
    $UNIX_DATE = ($row['DATE'] - 25569) * 86400;
    $date = gmdate("d/m/Y", $UNIX_DATE);

    $data = [
      'company_id'      =>  request()->company->id,
      'salesman_name'   =>  $row['SALESMAN NAME'],
      'beat_type'       =>  $row['BEAT TYPE'],
      'empl_id'         =>  $row['EMPL ID'],
      'day'             =>  $row['DAY'],
      'which_week'      =>  $row['WHICH WEEK'],
      'date'            =>  $date,
      'beat_name'       =>  $row['BEAT NAME'],
      'town'            =>  $row['TOWN'],
      'distributor'     =>  $row['DISTRIBUTOR'],
      'sales_officer'   =>  $row['SALES OFFICER'],
      'area_manager'    =>  $row['AREA MANAGER'],
      'region'          =>  $row['REGION'],
      'branch'          =>  $row['BRANCH'],
      'outlet_name'     =>  $row['OUTLET NAME'],
      'outlet_address'  =>  $row['OUTLET ADDRESS'],
      'uid'             =>  $row['UID'],
      'category'        =>  $row['CATEGORY'],
      'class'           =>  $row['CLASS'],
      'contact_person'  =>  $row['CONTACT PERSON'],
      'mobile_no'       =>  $row['MOBILE NO'],
      'landline_no'     =>  $row['LANDLINE NO'],
      'mail_id'         =>  $row['MAIL ID'],
      'address'         =>  $row['ADDRESS'],
      'regional'        =>  $row['REGIONAL MANAGER'],
      'national'        =>  $row['NATIONAL MANAGER'],
      'email'           =>  $row['EMAIL'],
    ];

    return new CrudeMaster($data);
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

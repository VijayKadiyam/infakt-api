<?php

namespace App\Imports;

use App\CrudeUserMapping;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class UserMappingImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if ($row['Store Name'] != '') {
            $data = [
                // 'crude_users column name' = $row['Excel column name']
                'company_id'  =>  request()->company->id,
                'emp_id' =>  $row['EMP ID'],
                'region'      =>  $row['Region'],
                'channel'     =>  $row['Channel'],
                'chain_name'        =>  $row['Chain Name'],
                'billing_code'       =>  $row['Billing Code'],
                'store_code'       =>  $row['Store Code'],
                'store_name'        =>  $row['Store Name'],
                'store_address'       =>  $row['Store Address'],
                'ba_name'       =>  $row['BA Name'],
                'location'        =>  $row['Location'],
                'city'       =>  $row['City'],
                'state'       =>  $row['State'],
                'rsm'        =>  $row['RSM'],
                'asm'       =>  $row['ASM'],
                'supervisor_name'       =>  $row['Supervisor Name'],
                'store_type'        =>  $row['Store Type'],
                'brand'       =>  $row['BRAND'],
                'ba_status'       =>  $row['BA status'],
                'store_status'       =>  $row['Store Status'],
                'user_login_id'       =>  $row['User id'],
                'user_password'       =>  $row['Pasword'],
                'remark'       =>  $row['Remarks'],
            ];

            return new CrudeUserMapping($data);
        }
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

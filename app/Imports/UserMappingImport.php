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
                'store_type'        => (array_key_exists('Store Type', $row) && $row['Store Type'])  ? $row['Store Type'] : NULL,
                'brand'       => (array_key_exists('BRAND', $row) && $row['BRAND']) ? $row['BRAND'] : NULL,
                'ba_status'       => (array_key_exists('BA status', $row) && $row['BA status'])  ? $row['BA status'] : NULL,
                'store_status'       => (array_key_exists('Store Status', $row) && $row['Store Status'])  ? $row['Store Status'] : NULL,
                'user_login_id'       => (array_key_exists('User id', $row) && $row['User id'])  ? $row['User id'] : NULL,
                'user_password'       => (array_key_exists('Pasword', $row) && $row['Pasword']) ? $row['Pasword'] : NULL,
                'remark'       => (array_key_exists('Remarks', $row) && $row['Remarks']) ? $row['Remarks'] : NULL,
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

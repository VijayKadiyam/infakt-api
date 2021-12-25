<?php

namespace App\Imports;

use App\CrudeCustomer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');

class CustomerImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if ($row['Date'] != '' || $row['Store Code'] != '') {
            $data = [
                'company_id'  =>  request()->company->id,
                'store_code' =>  $row['Store Code'],
                'date'      => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['Date'])->format('Y-m-d'),
                'no_of_customer'      =>  $row['No Of Customers'],
                'no_of_billed_customer'  =>  $row['No of Billed Customers'],
                'more_than_two'  =>  $row['More than two'],
            ];
            return new CrudeCustomer($data);
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
